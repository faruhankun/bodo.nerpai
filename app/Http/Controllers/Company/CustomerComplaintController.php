<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerComplaint;
use App\Models\ComplaintDetail;
use App\Models\OutboundRequest;
use App\Models\Company\Inventory\Inventory;
use App\Models\Company\Product;
use App\Models\Sale;

class CustomerComplaintController extends Controller
{
    // Show form to log complaints
    public function create(Request $request)
    {
        $sales = Sale::with('outboundRequests')->findOrFail($request->sales_order_id);

        // Calculate the total received quantities for each product
        $productsWithReceivedQuantities = $sales->products->map(function ($product) use ($sales) {
            $totalReceivedQuantity = $sales->outboundRequests->reduce(function ($carry, $or) use ($product) {
                $receivedQuantities = collect($or->received_quantities);
                $productReceivedQty = $receivedQuantities[$product->id] ?? 0;
                return $carry + $productReceivedQty;
            }, 0);

            $product->received_quantity = $totalReceivedQuantity;
            return $product;
        });

        return view('customer_complaints.create', compact('sales', 'productsWithReceivedQuantities'));
    }

    // Store complaints
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_order_id' => 'required|exists:sales,id',
            'description' => 'nullable|string',
            'details' => 'required|array',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.type' => 'required|in:Damaged,Missing,Excess',
            'details.*.quantity' => 'nullable|integer|min:1',
            'details.*.description' => 'nullable|string',
        ]);

        $sales = Sale::with('outboundRequests')->findOrFail($validated['sales_order_id']);

        foreach ($validated['details'] as $detail) {
            $totalReceivedQuantity = $sales->outboundRequests->reduce(function ($carry, $or) use ($detail) {
                $receivedQuantities = collect($or->received_quantities);
                return $carry + ($receivedQuantities[$detail['product_id']] ?? 0);
            }, 0);

            if (isset($detail['quantity']) && $detail['quantity'] > $totalReceivedQuantity) {
                return back()->withErrors([
                    'error' => "Quantity for product ID {$detail['product_id']} exceeds the received quantity ({$totalReceivedQuantity})."
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $complaint = CustomerComplaint::create([
                'sales_order_id' => $validated['sales_order_id'],
                'description' => $validated['description'],
                'status' => 'Logged',
            ]);

            foreach ($validated['details'] as $detail) {
                ComplaintDetail::create(array_merge($detail, ['customer_complaint_id' => $complaint->id]));
            }
        });

        return redirect()->route('customer_complaints.index')->with('success', 'Complaint logged successfully.');
    }

    // List all complaints
    public function index()
    {
        $complaints = CustomerComplaint::with('salesOrder')->orderBy('created_at', 'desc')->get();
        return view('customer_complaints.index', compact('complaints'));
    }

    // Show complaint details
    public function show(CustomerComplaint $customerComplaint)
    {
        $customerComplaint->load('details.product', 'salesOrder');
        
        return view('customer_complaints.show', compact('customerComplaint'));
    }

    // Resolve complaints
    public function resolve(Request $request, CustomerComplaint $customerComplaint)
    {
        $validated = $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'required|exists:complaint_details,id',
            'details.*.action' => 'required|in:Resolve,Resend,Return',
        ]);

        DB::transaction(function () use ($validated, $customerComplaint) {
            foreach ($validated['details'] as $detail) {
                $complaintDetail = ComplaintDetail::findOrFail($detail['id']);

                if ($detail['action'] === 'Resend') {
                    // Create a new Outbound Request for replacements
                    OutboundRequest::create([
                        'sales_order_id' => $customerComplaint->sales_order_id,
                        'warehouse_id' => $customerComplaint->salesOrder->warehouse_id,
                        'requested_quantities' => [$complaintDetail->product_id => $complaintDetail->quantity],
                        'status' => 'Requested',
                        'notes' => 'Replacement for complaint ID: ' . $customerComplaint->id,
                    ]);
                } elseif ($detail['action'] === 'Return') {
                    // Adjust inventory for returned items
                    $inventory = Inventory::where('product_id', $complaintDetail->product_id)
                        ->where('warehouse_id', $customerComplaint->salesOrder->warehouse_id)
                        ->first();

                    $inventory->quantity += $complaintDetail->quantity;
                    $inventory->save();
                }

                $complaintDetail->update(['status' => 'Resolved']);
            }

            // Mark the overall complaint as resolved if all details are resolved
            if ($customerComplaint->details()->where('status', '!=', 'Resolved')->count() === 0) {
                $customerComplaint->update(['status' => 'Resolved']);
            }
        });

        return redirect()->back()->with('success', 'Complaint resolved successfully.');
    }
}
