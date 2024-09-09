<!-- resources/views/admin/products/trashed.blade.php -->


@section('content')
<div class="container">
    <h1>Trashed Products</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.products.index') }}" class="btn btn-primary mb-3">Back to Products</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>
                        @if($product->price)
                            ${{ number_format(floatval($product->price), 2) }}
                        @else
                            No Price
                        @endif
                    </td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/images/' . basename($product->image)) }}" alt="{{ $product->name }}" width="100">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No trashed products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
