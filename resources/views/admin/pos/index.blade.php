
<div class="container">
    <h1>POS Transaction</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.pos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="order_date">Order Date</label>
            <input type="date" name="order_date" id="order_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                @foreach(\App\Models\Order::PAYMENT_METHODS as $method)
                    <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" name="status" id="status" class="form-control" readonly>
        </div>

        <div class="form-group">
            <label for="discount_code">Discount Code</label>
            <input type="text" name="discount_code" id="discount_code" class="form-control">
            <input type="hidden" name="discount" id="discount" value="0">
        </div>

        <h3>Order Items</h3>
        <div id="order-items">
            <!-- Dynamically add order items here -->
        </div>

        <div class="form-group">
            <label for="total">Total</label>
            <input type="text" name="total" id="total" class="form-control" readonly>
        </div>

        <button type="button" id="add-item" class="btn btn-primary">Add Item</button>
        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($products);
    const orderItemsContainer = document.getElementById('order-items');
    const addItemButton = document.getElementById('add-item');
    const paymentMethodSelect = document.getElementById('payment_method');
    const statusInput = document.getElementById('status');
    const discountCodeInput = document.getElementById('discount_code');
    const discountInput = document.getElementById('discount');
    const totalInput = document.getElementById('total');

    let itemCount = 0;
    let total = 0;

    addItemButton.addEventListener('click', function () {
        itemCount++;
        const itemDiv = document.createElement('div');
        itemDiv.className = 'form-group';
        itemDiv.innerHTML = `
            <div class="item">
                <h4>Item ${itemCount}</h4>
                <label for="items[${itemCount}][product_id]">Product</label>
                <select name="items[${itemCount}][product_id]" class="form-control product-select" required>
                    ${products.map(product => `<option value="${product.id}" data-price="${product.price}">${product.name}</option>`).join('')}
                </select>
                <label for="items[${itemCount}][quantity]">Quantity</label>
                <input type="number" name="items[${itemCount}][quantity]" class="form-control quantity-input" required>
                <label for="items[${itemCount}][price]">Price</label>
                <input type="number" name="items[${itemCount}][price]" class="form-control price-input" readonly>
            </div>
            <hr>
        `;
        orderItemsContainer.appendChild(itemDiv);
    });

    orderItemsContainer.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const select = e.target;
            const priceInput = select.closest('.item').querySelector('.price-input');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            priceInput.value = price;
            calculateTotal();
        }

        if (e.target.classList.contains('quantity-input')) {
            calculateTotal();
        }
    });

    paymentMethodSelect.addEventListener('change', function () {
        statusInput.value = paymentMethodSelect.value === 'cash' ? 'completed' : 'pending';
    });

    discountCodeInput.addEventListener('change', function () {
        const code = discountCodeInput.value;
        fetch(`/api/discounts/${code}`)
            .then(response => response.json())
            .then(data => {
                discountInput.value = data.percentage || 0;
                calculateTotal();
            });
    });

    function calculateTotal() {
        total = 0;
        document.querySelectorAll('.item').forEach(itemDiv => {
            const quantity = parseFloat(itemDiv.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(itemDiv.querySelector('.price-input').value) || 0;
            total += quantity * price;
        });

        const discountPercentage = parseFloat(discountInput.value) || 0;
        const discountAmount = (discountPercentage / 100) * total;
        total -= discountAmount;

        totalInput.value = total.toFixed(2);
    }
});
</script>
