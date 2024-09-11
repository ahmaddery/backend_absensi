<div class="container">
    <h1>Keranjang Belanja</h1>

    {{-- Menampilkan keranjang belanja --}}
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
        <p>Keranjang belanja kosong.</p>
    @endif

    {{-- Tautan kembali ke POS --}}
    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">Kembali ke POS</a>
    <a href="{{ route('admin.pos.checkout') }}" class="btn btn-primary">Bayar</a>
</div>
