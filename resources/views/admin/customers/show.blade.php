
    <div class="container">
        <h1>Customer Details</h1>

        <div class="card">
            <div class="card-header">
                <h2>{{ $customer->name }}</h2>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong> {{ $customer->email }}</p>
                <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p><strong>Address:</strong> {{ $customer->address }}</p>
            </div>
        </div>

        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary mt-3">Back to List</a>
    </div>

