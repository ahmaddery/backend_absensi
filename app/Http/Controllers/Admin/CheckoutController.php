<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Discount;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout(Request $request)
    {
        // Ambil keranjang berdasarkan user aktif
        $cart = Cart::where('user_id', auth()->id())->with('items.product')->firstOrFail();
        
        // Ambil customer_id dari cart
        $customerId = $cart->customer_id;
    
        // Hitung total harga keranjang
        $totalHarga = $cart->items->sum(function ($item) {
            return str_replace(['Rp ', '.'], ['', ''], $item->product->price) * $item->quantity;
        });
    
        // Total setelah diskon (tanpa diskon)
        $totalAfterDiscount = $totalHarga;
    
        // Buat data transaksi
        $transactionDetails = [
            'order_id' => uniqid(), // Generate unique order ID
            'gross_amount' => $totalAfterDiscount,
        ];
    
        // Data item yang dibeli
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->product->id,
                'price' => (float) str_replace(['Rp ', '.'], ['', ''], $item->product->price),
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        })->toArray();
    
        // Data pembeli
        $customerDetails = [
            'first_name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ];
    
        // Data yang dikirim ke Midtrans
        $payload = [
            'transaction_details' => $transactionDetails,
            'item_details' => $items,
            'customer_details' => $customerDetails,
        ];
    
        // Generate Snap token dari Midtrans
        try {
            $snapToken = Snap::getSnapToken($payload);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error generating Snap token: ' . $e->getMessage());
        }
    
        // Simpan order di database
        DB::transaction(function () use ($cart, $transactionDetails, $totalAfterDiscount, $customerId) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => $customerId, // Ambil customer_id dari cart
                'order_date' => now(),
                'total' => $totalAfterDiscount,
                'discount' => 0, // Tidak ada diskon
                'payment_method' => 'midtrans',
                'status' => 'pending',
                'order_id' => $transactionDetails['order_id'],
                'gross_amount' => $totalAfterDiscount,
            ]);
    
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
    
                // Decrement product stock
                $product = $item->product;
                $product->stock -= $item->quantity;
                $product->save();
            }
    
            // Clear cart
            $cart->items()->delete();
        });
    
        return view('admin.pos.checkout', compact('snapToken'));
    }
    
    

    public function midtransCallback(Request $request)
    {
        try {
            // Extract data from the request
            $data = $request->all();
            Log::info('Midtrans Callback Data:', $data);

            // Extract relevant fields
            $order_id = $data['order_id'];
            $transaction_status = $data['transaction_status'];
            $transaction_id = $data['transaction_id'];
            $status_message = $data['status_message'];
            $status_code = $data['status_code'];
            $signature_key = $data['signature_key'];
            $settlement_time = $data['settlement_time'] ?? null; // handle optional fields
            $payment_type = $data['payment_type'];
            $gross_amount = $data['gross_amount'];
            $fraud_status = $data['fraud_status'];
            $currency = $data['currency'];
            $merchant_id = $data['merchant_id'];

            // Find the order by order_id
            $order = Order::where('order_id', $order_id)->first();

            if ($order) {
                // Update the order with the data from Midtrans
                $order->update([
                    'transaction_status' => $transaction_status,
                    'transaction_id' => $transaction_id,
                    'status_message' => $status_message,
                    'status_code' => $status_code,
                    'signature_key' => $signature_key,
                    'settlement_time' => $settlement_time,
                    'payment_type' => $payment_type,
                    'gross_amount' => $gross_amount,
                    'fraud_status' => $fraud_status,
                    'currency' => $currency,
                    'merchant_id' => $merchant_id,
                ]);

                // Handle payment status
                switch ($transaction_status) {
                    case 'success':
                        $order->update(['status' => 'paid']);
                        break;
                    case 'pending':
                        $order->update(['status' => 'pending']);
                        break;
                    case 'expire':
                        $order->update(['status' => 'expired']);
                        break;
                    case 'failed':
                        $order->update(['status' => 'failed']);
                        break;
                }
            } else {
                Log::error("Order with ID $order_id not found.");
            }

            return response()->json(['message' => 'Callback processed'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
