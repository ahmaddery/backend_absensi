<!DOCTYPE html>
<html>
<head>
    <title>Transaction Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa; margin: 0; padding: 0;">
    <div class="container" style="max-width: 600px; margin: 20px auto;">
        <div class="card" style="border-radius: 8px;">
            <div class="card-body" style="padding: 20px;">
                <h1 class="card-title">Hello {{ $customerName }},</h1>
                <p class="card-text">Thank you for your purchase!</p>
                
                <h2>Order Details</h2>
                <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                <p><strong>Status:</strong> {{ $order->status }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($order->gross_amount, 2, ',', '.') }}</p>

                <!-- Add more details as needed -->
                
                <p class="mt-4">Thank you for shopping with us!</p>
            </div>
        </div>
    </div>
</body>
</html>
