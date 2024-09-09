<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Products</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.products.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Product</a>
            <a href="{{ route('admin.products.trashed') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">View Trashed Products</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border border-gray-300 text-left">#</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Name</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Description</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Price</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Stock</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Image</th>
                        <th class="py-2 px-4 border border-gray-300 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-4 border border-gray-300">{{ $loop->iteration }}</td>
                            <td class="py-2 px-4 border border-gray-300">{{ $product->name }}</td>
                            <td class="py-2 px-4 border border-gray-300">{{ $product->description }}</td>
                            <td class="py-2 px-4 border border-gray-300"> {{ $product->price }}</td>
                            <td class="py-2 px-4 border border-gray-300">{{ $product->stock }}</td>
                            <td class="py-2 px-4 border border-gray-300">
                                @if($product->image)
                                    <img src="{{ asset('storage/images/' . basename($product->image)) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                                @else
                                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Default Product Image" class="w-24 h-24 object-cover">
                                @endif
                            </td>
                            <td class="py-2 px-4 border border-gray-300">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-sm">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
