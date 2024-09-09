<!-- resources/views/admin/discounts/show.blade.php -->
    <h1>Detail Diskon</h1>

    <div>
        <strong>Kode Diskon:</strong> {{ $discount->code }}
    </div>
    <div>
        <strong>Deskripsi:</strong> {{ $discount->description }}
    </div>
    <div>
        <strong>Persentase:</strong> {{ $discount->percentage }}%
    </div>
    <div>
        <strong>Status:</strong> {{ $discount->active ? 'Aktif' : 'Tidak Aktif' }}
    </div>

    <a href="{{ route('admin.discounts.edit', $discount) }}" class="button button-blue">Edit</a>
    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="button button-red">Hapus</button>
    </form>
    <a href="{{ route('admin.discounts.index') }}" class="button">Kembali</a>

