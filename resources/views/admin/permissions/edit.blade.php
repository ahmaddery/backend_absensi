<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit User Role</title>
    <style>
        /* Gaya dasar untuk mempercantik tampilan */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
            text-align: center;
            width: 100%;
        }

        a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Edit User Role</h1>

    @if (session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.permissions.update', [$user->id, urlencode($user->name)]) }}" method="POST">
        @csrf
        @method('PUT')
    
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
    
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
    
        <label for="userType">Role:</label>
        <select id="userType" name="userType">
            <option value="user" {{ $user->userType === 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $user->userType === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    
        <button type="submit">Update</button>
    </form>
    

    <a href="{{ route('admin.permissions.index') }}">Back to User List</a>
</body>
</html>
