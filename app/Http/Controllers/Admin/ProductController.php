<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        // Mengambil semua produk yang ada
        $products = Product::all();
    
        return view('admin.products.index', compact('products'));
    }
    

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price'); // Pastikan ini adalah float
        $product->stock = $request->input('stock');
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $product->image = Storage::url($imagePath);
        }
    
        $product->save();
    
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price'); // Pastikan ini adalah float
        $product->stock = $request->input('stock');
    
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public' . parse_url($product->image, PHP_URL_PATH));
            }
    
            $imagePath = $request->file('image')->store('public/images');
            $product->image = Storage::url($imagePath);
        }
    
        $product->save();
    
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete('public' . parse_url($product->image, PHP_URL_PATH));
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
    public function trashed()
{
    $products = Product::onlyTrashed()->get()->map(function ($product) {
        $product->price = (float) $product->price; // Cast price to float
        return $product;
    });

    return view('admin.products.trashed', compact('products'));
}

public function restore($id)
{
    $product = Product::withTrashed()->findOrFail($id);
    $product->restore();

    return redirect()->route('admin.products.index')->with('success', 'Product restored successfully.');
}
}
