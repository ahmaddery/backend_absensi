<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Permissions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>User Permissions</h1>

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->userType }}</td>
                  <!-- Bagian Actions -->
<td>
    @if ($user->email === 'reizandid@gmail.com')
        <span>Cannot Edit or Delete</span>
    @else
        <a href="{{ route('admin.permissions.edit', [$user->id, urlencode($user->name)]) }}">Edit</a> | 
        @if ($user->trashed())
            <form action="{{ route('admin.permissions.restore', $user->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" onclick="return confirm('Are you sure you want to restore this user?')">Restore</button>
            </form>
        @else
            <form action="{{ route('admin.permissions.destroy', $user->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
            </form>
        @endif
    @endif
</td>


                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
