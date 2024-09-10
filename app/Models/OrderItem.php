<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2', // Ensure this is correctly cast
    ];

    protected $dates = ['deleted_at'];

    // Format price as currency for display
    public function getPriceAttribute($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    // Remove formatting before saving to database
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) str_replace(['Rp ', '.'], ['', ''], $value);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
