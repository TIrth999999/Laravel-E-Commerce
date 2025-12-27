<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body{ background:#f8fafc; }
        .card{ border:0; border-radius:16px; box-shadow:0 8px 24px rgba(2,6,23,.08); }
        .btn-primary{ border-radius:12px; padding:.8rem 1.2rem; font-weight:600; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="h4 mb-2">Complete Payment</h2>
                        <p class="text-muted mb-1">Order: <strong>{{ $order->order_number }}</strong></p>
                        <p class="fs-5 mb-3">Total: <strong>₹{{ number_format($order->total, 2) }}</strong></p>

                        <div class="d-flex align-items-center justify-content-center gap-2 text-muted mb-3">
                            <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
                            <span>Opening Razorpay checkout (Test Mode)...</span>
                        </div>

                        <button id="rzp-button" class="btn btn-primary w-100">Pay Now</button>
                        <div class="small text-muted mt-2">
                            If the popup doesn’t open, check your browser popup blocker and click Pay Now again.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var options = {
            "key": "{{ $key_id }}",
            "amount": {{ $amount }},
            "currency": "INR",
            "name": "E-Commerce",
            "description": "Order #{{ $order->order_number }}",
            "order_id": "{{ $razorpay_order_id }}",
            "notes": {
                "local_order_number": "{{ $order->order_number }}"
            },
            "handler": function (response){
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("payment.success") }}';
                
                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                var orderId = document.createElement('input');
                orderId.type = 'hidden';
                orderId.name = 'razorpay_order_id';
                orderId.value = response.razorpay_order_id;
                form.appendChild(orderId);
                
                var paymentId = document.createElement('input');
                paymentId.type = 'hidden';
                paymentId.name = 'razorpay_payment_id';
                paymentId.value = response.razorpay_payment_id;
                form.appendChild(paymentId);
                
                var signature = document.createElement('input');
                signature.type = 'hidden';
                signature.name = 'razorpay_signature';
                signature.value = response.razorpay_signature;
                form.appendChild(signature);
                
                document.body.appendChild(form);
                form.submit();
            },
            "modal": {
                "ondismiss": function(){
                    window.location.href = "{{ route('cart.index') }}";
                }
            },
            "prefill": {
                "name": "{{ $order->customer_name }}",
                "email": "{{ $order->customer_email }}",
                "contact": "{{ $order->customer_phone ?? '' }}"
            },
            "theme": {
                "color": "#2563eb"
            }
        };
        var rzp = new Razorpay(options);
        document.getElementById('rzp-button').onclick = function(e){
            rzp.open();
            e.preventDefault();
        }

        // Auto-open once after navigating here from the checkout submit.
        window.addEventListener('load', function () {
            setTimeout(function () {
                try { rzp.open(); } catch (e) {}
            }, 300);
        });
    </script>
</body>
</html>

