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

    // Format harga menjadi format mata uang Indonesia (RP)
    public function getPriceAttribute($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    // Menghapus format mata uang saat menyimpan ke database
    public function setPriceAttribute($value)
    {
        // Hapus 'Rp ' dan titik dari nilai harga sebelum menyimpan ke database
        $this->attributes['price'] = (float) str_replace(['Rp ', '.'], ['', ''], $value);
    }

}
