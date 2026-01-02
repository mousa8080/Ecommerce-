<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Order;
use App\Enum\PaymentProvider;
use App\Enum\PaymentStatus;
use App\Models\Payment;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPayment(Request $request, Order $order)
    {
        $request->validate([
            'provider' => 'required|string|in:' . implode(',', PaymentProvider::cases()),
        ]);
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid order id',
            ], 404);
        }
        if (!$order->canBeAcceptPayment()) {
            return response()->json([
                'status' => false,
                'message' => 'order cannot be paid',
            ], 400);
        }
        $provider = PaymentProvider::from($request->input('provider'));
        if ($provider === PaymentProvider::STRIPE) {
            return $this->createStripePayment($order);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'payment provider not supported',

            ]);
        }
    }
    protected function createStripePayment(Order $order)
    {

        try {
            DB::beginTransaction();
            $payment = Payment::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'provider' => PaymentProvider::STRIPE,
                'amount' => $order->total,
                'currency' => 'USD',
                'status' => PaymentStatus::PENDING,
                'metadata' => [
                    'order_id' => $order->order_number,
                    'created_at' => now()->toIso8601String(),
                ]
            ]);
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($order->total * 100),
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->order_number,
                    'payment_id' => $payment->id,
                ],
                'discription' => 'Payment for order ' . $order->order_number,
            ]);
            $payment->update([
                'payment_intent_id' => $paymentIntent->id,
                'metadata' => array_merge($payment->metadata, [
                    'client_secret' => $paymentIntent->client_secret,
                ])
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'payment intent created',
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'payment_id' => $payment->id,
                'order_id' => $order->order_number,
                'publishable_key' => config('services.stripe.secret'),

            ]);
        } catch (ApiErrorException $e) {
            DB::rollBack();
            Log::error('stripe payment error' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function confirmPayment(Request $request, $paymtntId)
    {
        $payment = Payment::find($paymtntId);
        if (!$payment) {
            return response()->json([
                'status' => false,
                'message' => 'payment not found',
            ], 404);
        }
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'unauthorized',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'payment confirmed',
            'payment' => $payment,
            'order' => $payment->order
        ], 200);
    }
}
