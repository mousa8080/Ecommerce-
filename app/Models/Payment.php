<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enum\PaymentProvider;
use App\Enum\PaymentStatus;


class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'amount' => 'decimal',
        'provider' => PaymentProvider::class,
        'status' => PaymentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsCompleted($paymentIntentId, $metadata)
    {
        $this->update([
            'status' => PaymentStatus::COMPLETED,
            'payment_intent_id' => $paymentIntentId,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
            'completed_at' => now(),
        ]);
        $this->order->markAsPaid($paymentIntentId);
    }
    public function markAsFailed($metadata = [])
    {
        $this->update([
            'status' => PaymentStatus::FAILED,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
        $this->order->markAsFaild();
    }
    public function isFinal()
    {
        return in_array($this->status, [
            PaymentStatus::COMPLETED,
            PaymentStatus::FAILED,
            PaymentStatus::REFUNDED
        ]);
    }
}
