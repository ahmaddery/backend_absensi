<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Checkout</h1>

    <!-- Form untuk memasukkan kode diskon -->
    <form action="{{ route('checkout') }}" method="POST">
        @csrf
        <label for="discount_code">Kode Diskon:</label>
        <input type="text" id="discount_code" name="discount_code" value="{{ old('discount_code') ?? $discountCode }}">
        <button type="submit">Terapkan Diskon</button>
    </form>

    <!-- Tampilkan total sebelum diskon -->
    <p>Total sebelum diskon: Rp {{ number_format($totalBeforeDiscount, 0, ',', '.') }}</p>

    <!-- Tampilkan diskon jika ada -->
    @if($discountAmount > 0)
        <p>Diskon ({{ $discountCode }}): -Rp {{ number_format($discountAmount, 0, ',', '.') }}</p>
    @endif

    <!-- Tampilkan total setelah diskon -->
    <p>Total setelah diskon: Rp {{ number_format($totalAfterDiscount, 0, ',', '.') }}</p>

    <!-- Tombol untuk membayar -->
    <button id="pay-button">Pay</button>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            // Trigger Snap popup Midtrans
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    alert("Payment success!");
                    // Redirect or show success message
                },
                onPending: function(result){
                    alert("Waiting for your payment!");
                },
                onError: function(result){
                    alert("Payment failed!");
                },
                onClose: function(){
                    alert('You closed the popup without finishing the payment');
                }
            });
        };
    </script>
</body>
</html>
