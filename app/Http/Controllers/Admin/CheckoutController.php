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
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionDetails;
use App\Models\Customer;


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
    
        // Hitung total harga keranjang sebelum diskon
        $totalBeforeDiscount = $cart->items->sum(function ($item) {
            return str_replace(['Rp ', '.'], ['', ''], $item->product->price) * $item->quantity;
        });
    
        // Total setelah diskon (default: tanpa diskon)
        $totalAfterDiscount = $totalBeforeDiscount;
    
        // Inisialisasi diskon
        $discountAmount = 0;
        $discountCode = $request->input('discount_code');
    
        if ($discountCode) {
            // Cek apakah diskon dengan kode tersebut ada dan aktif
            $discount = Discount::where('code', $discountCode)->where('active', true)->first();
    
            if ($discount) {
                // Hitung jumlah diskon
                $discountAmount = ($totalBeforeDiscount * $discount->percentage) / 100;
                $totalAfterDiscount = $totalBeforeDiscount - $discountAmount;
            } else {
                return redirect()->back()->withErrors('Kode diskon tidak valid atau tidak aktif.');
            }
        }
    
        // Buat data transaksi untuk Midtrans (pastikan totalAfterDiscount digunakan)
        $transactionDetails = [
            'order_id' => uniqid(), // Generate unique order ID
            'gross_amount' => (int) $totalAfterDiscount, // Pastikan menggunakan total setelah diskon
        ];
    
        // Data item yang dibeli
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->product->id,
                'price' => (int) str_replace(['Rp ', '.'], ['', ''], $item->product->price),
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        })->toArray();
    
        // Jika diskon ada, tambahkan item diskon ke dalam daftar item yang dikirim ke Midtrans
        if ($discountAmount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => - (int) $discountAmount, // Diskon sebagai item negatif
                'quantity' => 1,
                'name' => 'Discount'
            ];
        }
    
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
        DB::transaction(function () use ($cart, $transactionDetails, $totalAfterDiscount, $discountAmount, $customerId, $discountCode) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => $customerId,
                'order_date' => now(),
                'total' => $totalAfterDiscount,
                'discount' => $discountAmount,
                'discount_code' => $discountCode, // Simpan kode diskon yang digunakan
                'payment_method' => 'midtrans',
                'status' => 'pending',
                'order_id' => $transactionDetails['order_id'],
                'gross_amount' => $totalAfterDiscount,
            ]);
    
            // Simpan setiap item yang ada di keranjang ke dalam order item
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
    
                // Kurangi stok produk
                $product = $item->product;
                $product->stock -= $item->quantity;
                $product->save();
            }
    
            // Bersihkan keranjang setelah checkout
          //  $cart->items()->delete();  // sudah di implementasikan di callback
        });
    
        return view('admin.pos.checkout', compact('snapToken', 'totalBeforeDiscount', 'totalAfterDiscount', 'discountAmount', 'discountCode'));
    }
    
    
    public function midtransCallback(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            Log::info('Midtrans Callback Request:', $request->all());
    
            // Extract data from the request
            $data = $request->all();
            $order_id = $data['order_id'] ?? null;
            $transaction_status = $data['transaction_status'] ?? null;
            $transaction_id = $data['transaction_id'] ?? null;
            $status_message = $data['status_message'] ?? null;
            $status_code = $data['status_code'] ?? null;
            $signature_key = $data['signature_key'] ?? null;
            $settlement_time = $data['settlement_time'] ?? null;
            $payment_type = $data['payment_type'] ?? null;
            $gross_amount = $data['gross_amount'] ?? null;
            $fraud_status = $data['fraud_status'] ?? null;
            $currency = $data['currency'] ?? null;
            $merchant_id = $data['merchant_id'] ?? null;
    
            // Check if order_id is missing
            if (!$order_id) {
                Log::error('Order ID not found in callback data');
                return response()->json(['message' => 'Order ID not found'], 400);
            }
    
            // Find the order by order_id
            $order = Order::where('order_id', $order_id)->first();
    
            // If order is not found, log and return error
            if (!$order) {
                Log::error("Order with ID $order_id not found.");
                return response()->json(['message' => 'Order not found'], 404);
            }
    
            // Validate the signature key (optional)
            $calculatedSignature = hash('sha512', $order_id . $status_code . $gross_amount . config('services.midtrans.server_key'));
            if ($calculatedSignature !== $signature_key) {
                Log::error('Invalid Signature Key');
                return response()->json(['message' => 'Invalid Signature Key'], 403);
            }
    
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
                case 'settlement':
                    // Update status to 'completed'
                    $order->update(['status' => 'completed']);
    
                    // Get customer email using customer_id from the order
                    $customer = Customer::find($order->customer_id);
    
                    // If customer not found, log and return error
                    if (!$customer) {
                        Log::error("Customer with ID {$order->customer_id} not found.");
                        return response()->json(['message' => 'Customer not found'], 404);
                    }
    
                    // Send transaction details to customer email
                    try {
                        Mail::to($customer->email)->send(new TransactionDetails($order, $customer->name));
                        Log::info("Transaction details sent to customer: {$customer->email}");
                    } catch (\Exception $e) {
                        Log::error('Failed to send email: ' . $e->getMessage());
                        return response()->json(['message' => 'Failed to send email'], 500);
                    }
    
                    // Clear cart after transaction is settled
                    $cart = Cart::where('user_id', $order->user_id)->first();
                    if ($cart) {
                        $cart->items()->delete(); // Hapus semua item di keranjang
                        $cart->delete(); // Opsional: Hapus keranjang itu sendiri
                    }
    
                    break;
    
                case 'pending':
                    // Update status to 'pending'
                    $order->update(['status' => 'pending']);
                    break;
    
                case 'cancel':
                    // Update status to 'cancelled'
                    $order->update(['status' => 'cancelled']);
                    break;
    
                case 'expire':
                    // Update status to 'expired'
                    $order->update(['status' => 'expired']);
                    break;
    
                case 'deny':
                case 'failure':
                    // Update status to 'failed'
                    $order->update(['status' => 'failed']);
                    break;
    
                default:
                    Log::warning("Unknown transaction status: {$transaction_status}");
            }
    
            return response()->json(['message' => 'Callback processed successfully'], 200);
    
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    
 
}
