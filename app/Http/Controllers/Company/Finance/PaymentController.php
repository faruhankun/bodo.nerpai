<?php

namespace App\Http\Controllers\Company\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Finance\Payment;
use App\Models\Company\Finance\PaymentDetail;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Company\Finance\Account;

use App\Services\Company\Finance\JournalEntryService;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::orderBy('updated_at', 'desc')->get();

        return view('company.finance.payments.index', compact('payments'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with('payment_details')->findOrFail($id);
        $payment_methods = Account::where('type_id', 1)->get();         // Kas & Bank

        return view('company.finance.payments.show', compact('payment', 'payment_methods'));
    }



    public function handleAction(Request $request, $id, $action){
		$payment = Payment::findOrFail($id);

		switch($action){
			case 'PAYMENT_PROCESS':
				$this->paymentProcess($payment);
				break;
            case 'PAYMENT_PAID':
                $this->paymentPaid($request, $payment);
                break;
			default:
				abort(404);
		}

		return redirect()->route('payments.show', $payment->id)->with('success', "Payment {$payment->number} updated successfully.");
	} 


    public function paymentProcess($payment){
        $payment->status = 'PYM_PROCESS';

        $payment->save();
    }


    public function paymentPaid(Request $request, $payment){
        $validated = $request->validate([
            'payment_method' => 'required|integer',
            'payment_proof' => 'nullable',
        ]);
        
        $payment->payment_method = $validated['payment_method'];

        $this->addPaymenttoJournal($payment);

        $payment->status = 'PYM_PAID';
        $payment->save();

        // update payment details
        foreach ($payment->payment_details as $detail) {
            $detail->invoice->status = 'paid';
            $detail->invoice->save();

            $detail->balance = $detail->amount;
            $detail->save();

            if($payment->type == 'AP'){
                $detail->invoice->payable->status = 'paid';
                $detail->invoice->payable->balance += $detail->amount;
                $detail->invoice->payable->save();
            } else if($payment->type == 'AR'){
                $detail->invoice->receivable->status = 'paid';
                $detail->invoice->receivable->balance += $detail->amount;
                $detail->invoice->receivable->save();
            } else if($payment->type == 'EXP'){
                $detail->invoice->status = 'paid';
                $detail->invoice->payment_method = $payment->payment_method;
                $detail->invoice->save();
            }
        }
    }


    public function addPaymenttoJournal($payment)
    {
        $employee = session('employee');

        // journal entry
        $journalServices = app(JournalEntryService::class);

        // journal details
        $details = [];
        switch($payment->type){
            case 'AP':
                $details = [
                    // Debit Hutang Usaha
                    [
                        'account_id' => get_company_setting('comp.account_payables'),                          // akun hutang usaha
                        'debit' => $payment->total_amount,
                    ],
        
                    // Kredit Kas & Bank
                    [
                        'account_id' => $payment->payment_method,     // Payment method
                        'credit' => $payment->total_amount,
                    ],
                ];
                break;
            case 'AR':
                $details = [
                    // Debit Kas & Bank
                    [
                        'account_id' => $payment->payment_method,     // payment method
                        'debit' => $payment->total_amount,
                    ],
        
                    // Kredit Piutang Usaha
                    [
                        'account_id' => get_company_setting('comp.account_receivables'),                              // akun piutang usaha
                        'credit' => $payment->total_amount,
                    ],
                ];
                break;
            case 'EXP':
                $details = [
                    // Debit Biaya
                    [
                        'account_id' => $payment->source->account_id,                              // akun biaya
                        'debit' => $payment->total_amount,
                    ],
        
                    // Kredit Kas & Bank
                    [
                        'account_id' => $payment->payment_method,     // payment method
                        'credit' => $payment->total_amount,
                    ],
                ];
                break;
            default: ;
        }

        // add journal entry
        $journalServices->addJournalEntry(
            [
                'date' => date('Y-m-d'),
                'description' => 'payment ' . $payment->number,
                'type' => 'PYM',
                'source_type' => 'PYM',
                'source_id' => $payment->id,
                'created_by' => $employee->id,
                'total' => $payment->total_amount,
            ], 
            $details
        );
    }
}
