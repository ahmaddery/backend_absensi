<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;

class PosController extends Controller
{
    // Menampilkan daftar produk
    public function index()
    {
        // Mengambil semua produk yang ada
        $products = Product::all();

        // Menampilkan view dengan daftar produk
        return view('admin.pos.index', compact('products'));
    }

    public function addToCart(Request $request, $productId)
    {
        // Validasi jumlah yang dimasukkan
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'customer_id' => 'nullable|exists:customers,id', // Validasi opsional
        ]);
    
        // Cari atau buat keranjang untuk user yang sedang aktif
        $cart = Cart::where('user_id', auth()->id())
                    ->where('customer_id', $request->customer_id)
                    ->first();
    
        // Jika tidak ada keranjang yang cocok, buat keranjang baru
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id, // Set customer_id jika ada
            ]);
        }
    
        // Cari produk berdasarkan ID
        $product = Product::findOrFail($productId);
    
        // Cek apakah produk sudah ada di dalam keranjang
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $product->id)
                            ->first();
    
        if ($cartItem) {
            // Jika produk sudah ada, tambahkan kuantitas
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Jika produk belum ada, tambahkan sebagai item baru
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }
    
        // Redirect kembali dengan pesan sukses
        return redirect()->route('admin.pos.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }
    
    

    public function showCart(Request $request)
    {
        $customerId = $request->input('customer_id');
        $query = Cart::where('user_id', auth()->id());
    
        // Filter berdasarkan customer_id jika ada
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
    
        // Ambil keranjang dengan relasi item dan produk
        $cart = $query->with('items.product')->first();
    
        // Menampilkan view keranjang
        return view('admin.pos.cart', compact('cart', 'customerId'));
    }
    
}

