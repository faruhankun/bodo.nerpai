<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Company\Product;
use App\Models\Store\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Enum;

// enum Status: string
// {
//     case Active = 'Active';
//     case Inactive = 'Inactive';
// }

class StoreProductController extends Controller
{
    public function index()
    {
        $store_id = Session::get('company_store_id');
        $store_products = StoreProduct::with('store', 'product')->where('store_id', $store_id)->get();

        $products = Product::where('status', 'Active')
            ->whereDoesntHave('storeProducts', function ($query) use ($store_id) {
                $query->where('store_id', $store_id);
            })->get();

        return view('store.store_products.index', compact('store_products', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'store_price' => 'nullable|numeric|min:0',
            'status' => ['required', new Enum(Status::class)],
            'notes' => 'nullable|text'
        ]);

        $store_id = Session::get('company_store_id');
        $store_product = StoreProduct::create(array_merge($validated, ['store_id' => $store_id]));

        return redirect()->route('store_products.index')->with('success', "Store Product {$store_product->product->name} created successfully.");
    }

    public function show(string $id)
    {
        $store_product = StoreProduct::with('product')->findOrFail($id);
        return view('store.store_products.show', compact('store_product'));
    }

    public function edit(string $id)
    {
        $store_product = StoreProduct::with('product')->findOrFail($id);
        return view('store.store_products.edit', compact('store_product'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'store_price' => 'nullable|numeric|min:0',
            'status' => ['required', new Enum(Status::class)],
            'notes' => 'nullable|text'
        ]);

        $store_product = StoreProduct::with('store')->findOrFail($id);
        $store_product->update($validated);

        return redirect()->route('store_products.show', $store_product->id)->with('success', "Store Product {$store_product->product->name} updated successfully.");
    }

    public function destroy(string $id)
    {
        $store_product = StoreProduct::with('store')->findOrFail($id);
        $store_product->delete();
        return redirect()->route('store_products.index')->with('success', "Store Product {$store_product->product->name} deleted successfully.");
    }
}
