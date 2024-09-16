@include('admin.layouts.header')
@include('admin.layouts.navbar')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
            <button onclick="openCreateModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Product</button>
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
                            <td class="py-2 px-4 border border-gray-300">{{ $product->price }}</td>
                            <td class="py-2 px-4 border border-gray-300">{{ $product->stock }}</td>
                            <td class="py-2 px-4 border border-gray-300">
                                @if($product->image)
                                    <img src="{{ asset('storage/images/' . basename($product->image)) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                                @else
                                    <img src="https://cdn-icons-png.flaticon.com/512/2331/2331970.png" alt="Default Product Image" class="w-24 h-24 object-cover">
                                @endif
                            </td>
                            <td class="py-2 px-4 border border-gray-300">
                                <button onclick="openEditModal({{ $product->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-sm">Edit</button>
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

    <!-- Modal HTML for Create Product -->
    <div id="createProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCreateModal()">&times;</span>
            <h2 class="text-2xl font-bold mb-4">Create New Product</h2>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded" required>
                    @error('name')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" id="price" name="price" step="0.01" value="{{ old('price') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded" required>
                    @error('price')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock') }}" class="mt-1 block w-full p-2 border border-gray-300 rounded" required>
                    @error('stock')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" id="image" name="image" class="mt-1 block w-full p-2 border border-gray-300 rounded">
                    @error('image')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Create Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal HTML for Edit Product -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
          
            <!-- Edit Product Form will be loaded here via AJAX -->
            <div id="editProductForm">
                <!-- Form will be injected here -->
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createProductModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createProductModal').style.display = 'none';
        }

        function openEditModal(productId) {
            // Use AJAX to load the edit form into the modal
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `/admin/products/${productId}/edit`);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('editProductForm').innerHTML = xhr.responseText;
                    document.getElementById('editProductModal').style.display = 'block';
                }
            };
            xhr.send();
        }

        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeCreateModal();
                closeEditModal();
            }
        }
    </script>
</body>
</html>
