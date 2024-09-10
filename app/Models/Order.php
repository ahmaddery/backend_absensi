<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_date',
        'total',
        'payment_method',
        'status',
        'discount', // Ensure this is included
        'order_id',
        'purchase_date',
        'transaction_time',
        'transaction_status',
        'transaction_id',
        'status_message',
        'status_code',
        'signature_key',
        'settlement_time',
        'payment_type',
        'gross_amount',
        'fraud_status',
        'currency',
        'merchant_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2', // Ensure this is cast properly
        'order_date' => 'datetime',
        'purchase_date' => 'datetime',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'gross_amount' => 'decimal:2',
        'payment_method' => 'string',
        'status' => 'string',
        'transaction_status' => 'string',
        'transaction_id' => 'string',
        'status_message' => 'string',
        'status_code' => 'string',
        'signature_key' => 'string',
        'payment_type' => 'string',
        'fraud_status' => 'string',
        'currency' => 'string',
        'merchant_id' => 'string',
    ];

    const PAYMENT_METHODS = ['cash', 'payment_gateway'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
