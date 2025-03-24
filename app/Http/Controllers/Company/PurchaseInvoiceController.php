<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\PurchaseInvoice;
use App\Models\Company\Finance\Payable;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Company\Finance\JournalEntryDetail;
use App\Models\Company\Finance\Account;

use App\Services\Company\Finance\JournalEntryService;

class PurchaseInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchase_invoice = PurchaseInvoice::with('purchase')->findOrFail($id);

        return view('company.finance.purchase_invoices.show', compact('purchase_invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase_invoice = PurchaseInvoice::with('purchase')->findOrFail($id);

        return view('company.finance.purchase_invoices.edit', compact('purchase_invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'cost_products' => 'required|numeric',
            'vat_input' => 'required|numeric',
            'cost_packing' => 'required|numeric',
            'cost_insurance' => 'required|numeric',
            'cost_freight' => 'required|numeric',
            'status' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $purchase_invoice = PurchaseInvoice::findOrFail($id);
        $purchase_invoice->update($validated);
        $purchase_invoice->generateInvoiceNumber();

        // update total amount
        $purchase_invoice->total_amount = $purchase_invoice->cost_products + 
                                        $purchase_invoice->vat_input +
                                        $purchase_invoice->cost_packing + 
                                        $purchase_invoice->cost_insurance + 
                                        $purchase_invoice->cost_freight;
        $purchase_invoice->save();

        $purchase = $purchase_invoice->purchase;
        return redirect()->route('purchases.show', $purchase->id)->with('success', "Invoice {$purchase_invoice->number} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase_invoice = PurchaseInvoice::findOrFail($id);
        $purchase_invoice->forceDelete();

        // back to url before
        return redirect()->to(url()->previous())->with('success', "Invoice {$purchase_invoice->number} deleted successfully.");
    }


    public function handleAction(Request $request, $id, $action){
		$invoice = PurchaseInvoice::findOrFail($id);

		switch($action){
			case 'unpaid':
				$this->confirmInvoice($invoice);
				break;
			default:
				abort(404);
		}

		return redirect()->route('purchase_invoices.show', $invoice->id)->with('success', "Invoice {$invoice->number} updated successfully.");
	} 

    
    public function confirmInvoice($invoice)
    {
        // Register to AP
        $this->addInvoicetoAP($invoice);
        $this->addInvoicetoJournal($invoice);

        $invoice->status = 'unpaid';
        $invoice->save();
    }


    public function addInvoicetoAP($invoice)
    {
        $payable = Payable::create([
            'purchase_invoice_id' => $invoice->id,
            'supplier_id' => $invoice->purchase->supplier_id,
            'total_amount' => $invoice->total_amount,
            'status' => 'unconfirmed',
        ]);
    }


    public function addInvoicetoJournal($invoice)
    {
        $employee = session('employee');

        $journalService = app(JournalEntryService::class);
        $journalService->addJournalEntry([
            'source_type' => 'POI',
            'source_id' => $invoice->id,
            'date' => date('Y-m-d'),
            'type' => 'AP',
            'description' => 'uang muka purchase',
            'total' => $invoice->total_amount,
            'created_by' => $employee->id,
        ], [
            [
                'account_id' => get_company_setting('comp.account_downpayment_supplier'),                  // uang muka
                'debit' => $invoice->cost_products,
            ],
            [
                'account_id' => get_company_setting('comp.account_vat_input'),                  // ppn masukan
                'debit' => $invoice->vat_input,
            ],
            [
                'account_id' => get_company_setting('comp.account_shipping_freight'),                  // biaya pengiriman dan pengangkutan
                'debit' => $invoice->cost_freight + $invoice->cost_packing + $invoice->cost_insurance,
            ],
            [
                'account_id' => get_company_setting('comp.account_payables'),                  // hutang supplier
                'credit' => $invoice->total_amount,
            ],
        ]);
    }
}
