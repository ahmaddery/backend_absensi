<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $casts = [
        'price' => 'float',
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Jika Anda menggunakan guarded
    // protected $guarded = [];

    // Untuk mengatur format harga
    public function getPriceAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }

    // Untuk menyimpan format harga
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = str_replace(',', '', $value);
    }





}
