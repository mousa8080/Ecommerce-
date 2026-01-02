<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItmes = Cart::with('product')->where('user_id', $user->id)->get();
        $total = $cartItmes->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        return response()->json([
            'status' => true,
            'message' => 'Cart items retrieved successfully',
            'cart_items' => $cartItmes,
            'total' => $total
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $user = Auth::user();
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();
        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Cart item added successfully',
            'cart_item' => $cartItem,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {

        return response()->json([
            'status' => true,
            'message' => 'Cart item retrieved successfully',
            'cart_item' => $cart,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cart->quantity = $request->quantity;
        $cart->save();
        return response()->json([
            'status' => true,
            'message' => 'Cart item updated successfully',
            'cart_item' => $cart,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json([
            'status' => true,
            'message' => 'Cart item removed successfully',
        ], 200);
    }
    public function clearCart(Request $request)
    {
        $user = Auth::user();
        Cart::where('user_id', $user->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Cart cleared successfully',
        ], 200);
    }
}
