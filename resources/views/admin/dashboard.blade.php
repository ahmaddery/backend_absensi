<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
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
</head>
<body>
    <h1>Halaman Admin</h1>
    <a href="{{ route('admin.permissions.index') }}" class="button">Kelola Roles</a>
    <a href="{{ route('admin.products.index') }}" class="button button-red">Kelola Produk</a>
    <a href="{{ route('admin.customers.index') }}" class="button button-blue">Kelola Customers</a>
    <a href="{{ route('admin.discounts.index') }}" class="button button-blue">Kelola diskon</a>
    <a href="{{ route('pos.index') }}" class="button button-blue">Kelola pos</a>
</body>
</html>
