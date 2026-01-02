<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItme;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;





class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_Zipcode',
        'shipping_country',
        'shipping_email',
        'shipping_phone',
        'subtotal',
        'tax',
        'shipping_cost',
        'total',
        'payment_method',
        'order_number',
        'note',
        'transaction_id',
        'paid_at',
    ];
    protected $casts = [
        'payment_status' => PaymentStatus::class,
        'order_status' => OrderStatus::class,
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItme::class);
    }
    public function canBeCancelled()
    {
        in_array($this->status, [OrderStatus::PENDING, OrderStatus::PAID]);
    }
    public function markAsPaid($transactionId)
    {
        $this->update([
            'status' => OrderStatus::PAID,
            'payment_status' => PaymentStatus::COMPLETED,
            'transaction_id' => $transactionId,
            'paid' => now(),
        ]);
    }
    public function markAsFaild()
    {
        $this->update([
            'payment_status' => PaymentStatus::FAILED,
        ]);
    }
    public static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $year = date('Y');
            $randomNumber = strtoupper(substr(uniqid(), -6));
            $order->order_number = "ORD-{$year}-{$randomNumber}";
        });
    }
}
