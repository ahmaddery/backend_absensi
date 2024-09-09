<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $customers = Customer::all();
        $discounts = Discount::all();

        return view('admin.pos.index', compact('products', 'customers', 'discounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'payment_method' => 'required|in:cash,payment_gateway',
            'status' => 'required|string',
            'discount_code' => 'nullable|exists:discounts,code',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Get discount if applicable
        $discount = null;
        if ($request->discount_code) {
            $discount = Discount::where('code', $request->discount_code)->first();
        }

        $discountPercentage = $discount ? $discount->percentage : 0;

        // Determine status based on payment method
        $status = $request->payment_method === 'cash' ? 'completed' : 'pending';

        DB::beginTransaction();
        try {
            $total = array_sum(array_column($request->items, 'price'));
            $discountAmount = ($discountPercentage / 100) * $total;
            $finalTotal = $total - $discountAmount;

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'payment_method' => $request->payment_method,
                'status' => $status,
                'total' => $finalTotal,
                'discount' => $discountAmount,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create transaction.');
        }
    }
}
