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
        'discount',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'order_date' => 'datetime',
        'payment_method' => 'string',
        'status' => 'string',
    ];

    const PAYMENT_METHODS = ['cash', 'payment_gateway'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
