<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    // Menampilkan semua cart berdasarkan customer_id
    public function index(Request $request)
    {
        $customerId = $request->query('customer_id');

        if (!$customerId) {
            return response()->json(['error' => 'Customer ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $carts = Cart::where('customer_id', $customerId)->with('items')->get();

        return response()->json($carts, Response::HTTP_OK);
    }

    // Membuat cart baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $cart = Cart::create($request->all());

        return response()->json($cart, Response::HTTP_CREATED);
    }

    // Mengupdate cart
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'customer_id' => 'sometimes|exists:customers,id',
        ]);

        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], Response::HTTP_NOT_FOUND);
        }

        $cart->update($request->all());

        return response()->json($cart, Response::HTTP_OK);
    }

    // Menghapus cart
    public function destroy($id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], Response::HTTP_NOT_FOUND);
        }

        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    // Menampilkan item cart berdasarkan cart_id
    public function showCartItems($cartId)
    {
        $cartItems = CartItem::where('cart_id', $cartId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'No items found for this cart'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($cartItems, Response::HTTP_OK);
    }

    // Menambah item ke cart berdasarkan customer_id
    public function addCartItem(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Temukan cart berdasarkan customer_id
        $cart = Cart::where('customer_id', $request->input('customer_id'))->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found for this customer'], Response::HTTP_NOT_FOUND);
        }

        // Validasi produk
        $product = Product::find($request->input('product_id'));

        if (!$product) {
            return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        // Tambah item ke cart
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $request->input('quantity'),
        ]);

        return response()->json($cartItem, Response::HTTP_CREATED);
    }
}