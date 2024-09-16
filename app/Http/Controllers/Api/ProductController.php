<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan semua data produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Ambil semua data produk
        $products = Product::all();

        // Kembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $products
        ], 200);
    }
}
