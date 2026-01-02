<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductReqeust;
use App\Http\Requests\UpdateProductReqeust;
use App\Models\Product;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Cache()->remember('products', 300, function () {
            return Product::with(relations: 'categorys')->filter($filter = request()->query())->paginate();
        });

        return response()->json([
            'status' => true,
            'message' => 'product retrieved successfully',
            'products' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReqeust $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {

            $data['image'] = $request->file('image')->storeAs('products', '' . $request->file('image')->getClientOriginalName() . '.' . $request->file('image')->getClientOriginalExtension(), 'public', 'public');
        }
        $product = Product::create($data);
        $product->categorys()->attach($request->categorys);
        $product->load('categorys');
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
        $product = Cache::remember("product_{$product->id}", 300, function () use ($product) {
            return $product->load('categorys');
        });
        $product->load('categorys');

        return response()->json([
            'status' => true,
            'message' => 'product retrieved successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductReqeust $request, Product $product)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $filename = $product->SKU . '.' . $request->file('image')->getClientOriginalExtension();
            $data['image'] = $request->file('image')->storeAs('products', $filename, 'public');
        }
        Cache::forget("product_{$product->id}");
        Cache::forget('products');

        $product->update($data);
        $product->categorys()->sync($request->categorys);
        $product->load('categorys');
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
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        Cache::forget("product_{$product->id}");
        Cache::forget('products');
        $product->delete();
        return response()->json([
            'status' => true,
            'message' => 'product deleted successfully'
        ], 200);
    }

    public function restore(Request $request, Product $product)
    {
        if ($request->user()->hasRole('admin')) {
            $product->restore();
            return response()->json([
                'status' => true,
                'message' => 'product restored successfully'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'unauthorized'
        ], 403);
    }
    public function forceDelete(Request $request, Product $product)
    {
        if ($request->user()->hasRole('admin')) {
            $product->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'product permanently deleted successfully'
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'unauthorized'
        ], 403);
    }
    public function softDeletedProducts(Request $request, Product $product)
    {
        if ($request->user()->hasRole('admin')) {
            $product->onlyTrashed();
            return response()->json([
                'status' => true,
                'message' => 'trashed product retrieved successfully',
                'product' => $product
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'unauthorized'
        ], 403);
    }
    //filter products name and description and min and max price
    public function filter(Request $request)
    {
        $products = Product::query()
            ->when($request->input('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($request->input('description'), function ($query, $description) {
                $query->where('description', 'like', "%{$description}%");
            })
            ->when($request->input('min_price'), function ($query, $min_price) {
                $query->where('price', '>=', $min_price);
            })
            ->when($request->input('max_price'), function ($query, $max_price) {
                $query->where('price', '<=', $max_price);
            })->get();
        return response()->json([
            'status' => true,
            'message' => 'filtered products retrieved successfully',
            'products' => $products
        ], 200);
    }
}
