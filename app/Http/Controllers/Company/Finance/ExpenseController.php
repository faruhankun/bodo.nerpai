<?php

namespace App\Http\Controllers\Company\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\Expense;
use App\Models\Company\Finance\Payment;
use App\Models\Company\Finance\PaymentDetail;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // expense yang punya payable
        $expenses = Expense::with('consignee')->orderBy('updated_at', 'desc')->get();

        return view('company.finance.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $types = Expense::get_types();

        return view('company.finance.expenses.create', compact('types'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required',
            'type' => 'required',
            'amount' => 'required',
            'description' => 'nullable',
            'notes' => 'nullable',
        ]);

        $employee = session('employee');
        $expense = Expense::create([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'notes' => $validated['notes'],

            'requested_by' => $employee->id,
            'status' => 'requested',
        ]);
        $expense->generateNumber();
        $expense->save();

        return redirect()->route('expenses.show', $expense->id)->with('success', "Expense {$expense->number} created successfully.");
    }

    public function edit(string $id)
    {
        abort(404);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with('consignee', 'account', 'requestedby', 'approvedby')->findOrFail($id);
        $accounts = Account::with('account_type', 'parent', 'children')->get();
        $payment_methods = $accounts->where('type_id', 1);                      // Kas & Bank
        $expense_accounts = $accounts->where('type_id', 14);                   // Biaya Operasional

        //dd($expense);

        return view('company.finance.expenses.show', compact('expense', 'payment_methods', 'expense_accounts'));
    }


    public function handleAction(Request $request, $id, $action){
		$expense = Expense::findOrFail($id);

		switch($action){
			case 'approved':
            case 'rejected':
                $this->processExpense($expense, $action);
                break;
            case 'process':
                $validated = $request->validate([
                    'account_id' => 'required|exists:accounts,id',
                ]);

                $expense->account_id = $validated['account_id'];
				$this->makePaymentRequestforExpense($expense);
				break;
			default:
				abort(404);
		}

		return redirect()->route('expenses.show', $expense->id)->with('success', "Expense {$expense->number} updated successfully.");
	} 


    public function processExpense($expense, $action){
        $expense->status = $action;
        
        // notify requester, consignee
        if($action == 'approved'){
            $expense->approved_by = session('employee')->id;
        }

        $expense->save();
    }

    public function makePaymentRequestforExpense($expense)
    {
        // make payment
        $payment = Payment::create([
            'date' => date('Y-m-d'),
            'type' => 'EXP',
            'source_type' => 'EXP',
            'source_id' => $expense->id,
            'status' => 'PYM_PENDING',
            'notes' => 'Payment Request',
            'total_amount' => $expense->amount,
        ]);
        $payment->generateNumber();

        $payment_details = [
            'payment_id' => $payment->id,
            'invoice_type' => 'EXP',
            'invoice_id' => $expense->id,
            'amount' => $expense->amount,
            'balance' => 0,
        ];

        $payment->payment_details()->create($payment_details);

        $payment->save();

        $expense->status = 'process';
        $expense->save();
    }
}
