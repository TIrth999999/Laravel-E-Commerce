<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="text-success">Payment Successful!</h2>
                        <p>Order Number: {{ $order->order_number }}</p>
                        <p>Total Amount: ₹{{ $order->total }}</p>
                        <p>
                            Payment ID:
                            {{ $order->stripe_payment_intent_id ?? $order->razorpay_payment_id ?? 'N/A' }}
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

