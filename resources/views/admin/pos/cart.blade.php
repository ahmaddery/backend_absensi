{{-- resources/views/admin/pos/cart.blade.php --}}

<div class="container">
    <h1>Keranjang Belanja</h1>

    {{-- Dropdown untuk memilih customer --}}
    <form method="GET" action="{{ route('admin.pos.show-cart') }}" class="mb-4">
        <div class="form-group">
            <label for="customer_id">Pilih Customer:</label>
            <select name="customer_id" id="customer_id" class="form-control" onchange="this.form.submit()">
                <option value="">Guest</option>
                @foreach(App\Models\Customer::all() as $customer)
                    <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if ($cart && $cart->items->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalHarga = 0;
                @endphp
                @foreach ($cart->items as $item)
                    @php
                        $hargaSatuan = (float) str_replace(['Rp ', '.'], ['', ''], $item->product->price);
                        $kuantitas = $item->quantity;
                        $totalItem = $hargaSatuan * $kuantitas;
                        $totalHarga += $totalItem;
                    @endphp
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $kuantitas }}</td>
                        <td>{{ $item->product->price }}</td>
                        <td>{{ 'Rp ' . number_format($totalItem, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Keseluruhan:</strong></td>
                    <td>{{ 'Rp ' . number_format($totalHarga, 0, ',', '.') }}</td>
                </tr>
                @if ($cart->customer)
                    <tr>
                        <td colspan="3" class="text-right"><strong>Customer:</strong></td>
                        <td>{{ $cart->customer->name }}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
    @else
        <p>Keranjang belanja untuk {{ $customerId ? App\Models\Customer::find($customerId)->name : 'Guest' }} kosong.</p>
    @endif

    {{-- Tautan kembali ke POS --}}
    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">Kembali ke POS</a>
    <a href="{{ route('admin.pos.checkout') }}" class="btn btn-primary">Bayar</a>
</div>