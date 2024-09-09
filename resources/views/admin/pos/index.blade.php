{{-- resources/views/admin/pos/index.blade.php --}}

<div class="container">
    <h1>Point of Sale (POS)</h1>

    {{-- Pesan sukses jika produk berhasil ditambahkan ke keranjang --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($product->image)
                        <img src="{{ asset('storage/images/' . basename($product->image)) }}" alt="{{ $product->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Default Product Image" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <p class="card-text"><strong>Price:</strong> {{ $product->price }}</p>

                        {{-- Form untuk menambahkan produk ke keranjang --}}
                        <form action="{{ route('admin.pos.add-to-cart', $product->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                            </div>

                            {{-- Tambahkan dropdown customer --}}
                            <div class="form-group">
                                <label for="customer_id">Customer:</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">Tidak ada customer</option>
                                    @foreach(App\Models\Customer::all() as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tautan untuk melihat keranjang --}}
    <a href="{{ route('admin.pos.show-cart') }}" class="btn btn-success">View Cart</a>
</div>
