<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function show($code)
    {
        $discount = Discount::where('code', $code)->first();
        return response()->json([
            'percentage' => $discount ? $discount->percentage : 0
        ]);
    }
}
