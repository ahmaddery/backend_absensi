<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Tampilkan daftar diskon.
     */
    public function index()
    {
        $discounts = Discount::all();
        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Tampilkan formulir untuk membuat diskon baru.
     */
    public function create()
    {
        return view('admin.discounts.create');
    }

    /**
     * Simpan diskon baru ke dalam database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code',
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean',
        ]);

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil dibuat.');
    }

    /**
     * Tampilkan detail diskon.
     */
    public function show(Discount $discount)
    {
        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Tampilkan formulir untuk mengedit diskon.
     */
    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Perbarui diskon di database.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:discounts,code,' . $discount->id,
            'description' => 'nullable|string',
            'percentage' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean',
        ]);

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    /**
     * Hapus diskon dari database.
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }
}
