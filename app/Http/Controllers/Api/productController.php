<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => true,
            'message' => 'product retrieved successfully',
            'products' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'SKU' => 'required|string|max:100|unique:products,SKU',
            'is_active' => 'boolean',
            'image' => 'nullable|string|max:255',
        ]);
        $product = Product::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'message' => 'product retrieved successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'SKU' => 'sometimes|required|string|max:100|unique:products,SKU,' . $product->id,
            'is_active' => 'boolean',
            'image' => 'nullable|string|max:255',
        ]);
        if ($request->has('name')) {
            $product->name = $request->name;;
        }
        if ($request->has('description')) $product->description = $request->description;
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('stock')) $product->stock = $request->stock;
        if ($request->has('SKU')) $product->SKU = $request->SKU;
        if ($request->has('is_active')) $product->is_active = $request->is_active;

        $product->save();
        return response()->json([
            'status' => true,
            'message' => 'product updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'status' => true,
            'message' => 'product deleted successfully'
        ], 200);
    }
}
