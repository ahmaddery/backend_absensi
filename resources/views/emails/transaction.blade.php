<!DOCTYPE html>
<html>
<head>
    <title>Transaction Details</title>
</head>
<body>
    <h1>Hello {{ $customerName }},</h1>
    <p>Thank you for your purchase!</p>
    <p>Order ID: {{ $order->order_id }}</p>
    <p>Status: {{ $order->status }}</p>
    <p>Total: Rp {{ number_format($order->gross_amount, 2, ',', '.') }}</p>
    <!-- Add more details as needed -->
</body>
</html>