@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-receipt"></i> Order Details - {{ $order->order_number }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $order->customer_address ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Payment Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Payment Status:</strong> 
                    <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
                @if($order->razorpay_payment_id)
                <p><strong>Payment ID:</strong> {{ $order->razorpay_payment_id }}</p>
                @endif
                @if($order->razorpay_order_id)
                <p><strong>Order ID:</strong> {{ $order->razorpay_order_id }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Taxes</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td><span class="badge bg-info">{{ $item->quantity }}</span></td>
                        <td>₹{{ number_format($item->price, 2) }}</td>
                        <td>₹{{ number_format($item->discount, 2) }}</td>
                        <td>
                            @foreach($item->product->taxes as $tax)
                            <span class="badge bg-secondary me-1">{{ $tax->name }} ({{ $tax->type }} - {{ $tax->value }}{{ $tax->type == 'percentage' ? '%' : '₹' }})</span>
                            @endforeach
                        </td>
                        <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Subtotal</th>
                        <th>₹{{ number_format($order->subtotal, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Discount</th>
                        <th class="text-success">-₹{{ number_format($order->discount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Tax Amount</th>
                        <th>₹{{ number_format($order->tax_amount, 2) }}</th>
                    </tr>
                    <tr class="table-primary">
                        <th colspan="5" class="text-end">Total</th>
                        <th class="fs-5">₹{{ number_format($order->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
