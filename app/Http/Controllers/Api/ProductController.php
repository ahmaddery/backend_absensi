<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Menampilkan data produk berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Cari produk berdasarkan ID
        $product = Product::find($id);

        // Cek apakah produk ditemukan
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // Kembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $product
        ], 200);
    } 

    public function search(Request $request)
    {
        // Validasi input pencarian
        $request->validate([
            'query' => 'nullable|string|max:255',
        ]);

        // Ambil parameter pencarian
        $query = $request->input('query');

        // Mulai query produk
        $products = Product::query();

        // Tambahkan kondisi pencarian berdasarkan nama, deskripsi, atau harga
        if ($query) {
            $products = $products->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('price', 'LIKE', "%{$query}%");
            });
        }

        // Terapkan paginasi untuk mencegah server overload
        $products = $products->get(); // 10 data per halaman

        // Kembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian produk berhasil diambil',
            'data' => $products
        ], 200);
    }


    public function checkPriceByBarcode($barcode)
{
    // Cari produk berdasarkan barcode
    $product = Product::where('barcode', $barcode)->first();

    // Cek apakah produk ditemukan
    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan',
        ], 404);
    }

    // Kembalikan response JSON dengan harga produk
    return response()->json([
        'success' => true,
        'message' => 'Data produk berhasil diambil',
        'data' => [
            'name' => $product->name,
            'price' => $product->price,
            'barcode' => $product->barcode,
        ],
    ], 200);
}

public function create(Request $request)
{
    // Validasi input yang diperlukan
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
        'barcode' => 'required|string|max:255|unique:products,barcode', // Pastikan barcode unik
    ]);

    // Buat produk baru
    $product = new Product();
    $product->name = $request->input('name');
    $product->description = $request->input('description');
    $product->price = $request->input('price');
    $product->stock = $request->input('stock');
    $product->barcode = $request->input('barcode');

    // Cek dan simpan gambar jika ada
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('public/images'); // Menyimpan gambar ke storage
        $product->image = Storage::url($imagePath); // Mendapatkan URL gambar
    }

    $product->save(); // Simpan produk

    // Kembalikan response JSON dengan detail produk yang baru dibuat
    return response()->json([
        'success' => true,
        'message' => 'Produk berhasil dibuat',
        'data' => $product,
    ], 201);
}


}


