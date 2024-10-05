
    <style>
        /* Simple styling for the button */
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .button-red {
            background-color: #f44336; /* Red */
        }
    </style>

    <h1>Halaman Admin</h1>
    <a href="{{ route('admin.products.index') }}" class="button button-red">Kelola Produk</a>