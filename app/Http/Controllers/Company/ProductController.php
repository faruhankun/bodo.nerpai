<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Product;
use App\Services\ProductService;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $product;
    protected $productService;

    public function __construct(Product $product, ProductService $productService)
    {
        $this->product = $product;
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getProducts();
        return view('company.products.index', compact('products'));
    }

    public function create()
    {
        return view('company.products.create');
    }

    // Store the new product in the database
    public function store(StoreProductRequest $request)
    {
        try {
            $this->productService->createProduct($request->validated());

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'inputs' => $request->all(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('products.index')->with('error', 'An unexpected error occurred.');
        }
    }

    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        return view('company.products.show', compact('product'));
    }

    public function edit(String $id)
    {
        $product = $this->productService->getProduct($id);
        return view('company.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, String $id)
    {
        try {
            $this->productService->updateProduct($id, $request->validated());

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'inputs' => $request->all(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('products.index')->with('error', 'An unexpected error occurred.');
        }
    }

    public function destroy(string $id)
    {
        $this->productService->deleteProduct($id);
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
