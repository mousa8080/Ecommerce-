<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;

class checkoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        $user = Auth::user();
        $cartItmes = Cart::where('user_id', Auth::user()->id)->with('product')->get();
        if ($cartItmes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty',
            ], 200);
        }
        $subtotal = 0;
        $items = [];
        foreach ($cartItmes as $item) {
            $product = $item->product;
            if (!$product->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product is not active',
                ], 200);
            }
            if ($product->stock < $item->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product is out of stock',
                ], 200);
            }
            $itmeSubTotal = round($product->price * $item->quantity, 2);
            $subtotal += $itmeSubTotal;
            $items[] = [
                'product_name' => $product->name,
                'name' => $product->name,
                'price' => $product->price,
                'product_id' => $product->id,
                'image' => $product->image,
                'sku' => $product->SKU,
                'quantity' => $item->quantity,
                'total' => $itmeSubTotal,
                'subtotal' => $subtotal
            ];
        }
        $tax = round($subtotal * 0.1, 2);
        $shipping_cost = round(5, 2);
        $total = round($subtotal + $tax + $shipping_cost, 2);
        DB::beginTransaction();
        try {
            $order = new Order([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shipping_cost,
                'total' => $total,
                'status' => OrderStatus::PENDING,
                'payment_method' => $request->payment_method,
                'payment_status' => PaymentStatus::PENDING,
                'shipping_name' => $request->shipping_name,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_Zipcode' => $request->shipping_Zipcode,
                'shipping_country' => $request->shipping_country,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'note' => $request->note,
            ]);
            $user->orders()->save($order);
            foreach ($items as $item) {
                $order->orderItems()->create($item);
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }
            Cart::where('user_id', Auth::user()->id)->each(function ($cartItem) {
                $cartItem->delete();
            });
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Order placed successfully',
                'order' => $order->load('orderItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function orderHestory()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('orderItems')->get();
        return response()->json([
            'status' => true,
            'message' => 'Order history retrieved successfully',
            'user' => $user,
            'orders' => $orders,
        ], 200);
    }
    public function orderDetails(Request $request, $id)
    {
        $user = Auth::user();
        $order = $user->orders()->with('orderItems')->find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Order details retrieved successfully',
            'order' => $order->load('orderItems'),
        ], 200);
    }
}
