<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Checkout</h1>
    <button id="pay-button">Pay</button>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            // Trigger Snap popup
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
