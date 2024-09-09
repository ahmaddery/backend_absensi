<!-- resources/views/admin/discounts/index.blade.php -->


    <h1>Daftar Diskon</h1>

    <a href="{{ route('admin.discounts.create') }}" class="button">Tambah Diskon</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Deskripsi</th>
                <th>Persentase</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($discounts as $discount)
                <tr>
                    <td>{{ $discount->code }}</td>
                    <td>{{ $discount->description }}</td>
                    <td>{{ $discount->percentage }}%</td>
                    <td>{{ $discount->active ? 'Aktif' : 'Tidak Aktif' }}</td>
                    <td>
                        <a href="{{ route('admin.discounts.show', $discount) }}" class="button">Lihat</a>
                        <a href="{{ route('admin.discounts.edit', $discount) }}" class="button button-blue">Edit</a>
                        <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button button-red">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

