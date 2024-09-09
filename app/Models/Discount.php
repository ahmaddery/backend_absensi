<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    // Kolom-kolom yang boleh diisi (mass-assignable)
    protected $fillable = [
        'code',
        'description',
        'percentage',
        'active',
    ];

    /**
     * Tentukan nilai default untuk kolom tertentu.
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * Tentukan casting tipe data.
     */
    protected $casts = [
        'percentage' => 'decimal:2',
        'active' => 'boolean',
    ];
}
