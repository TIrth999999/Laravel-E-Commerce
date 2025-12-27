@extends('layouts.frontend')

@section('title', 'Shopping Cart')

@section('content')
<h1 class="h3 mb-4"><i class="bi bi-cart3"></i> Shopping Cart</h1>
@if($cartItems->count() > 0)
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cartItems as $item)
        <tr>
            <td>
                @if($item->product->images && count($item->product->images) > 0)
                @php
                    $img = $item->product->images[0];
                    $imgUrl = \Illuminate\Support\Str::startsWith($img, ['http://','https://']) ? $img : asset('storage/' . $img);
                @endphp
                <img src="{{ $imgUrl }}" alt="{{ $item->product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;" class="me-2">
                @endif
                <div class="d-inline-block align-middle">
                    <a href="{{ route('product', $item->product->id) }}" class="text-decoration-none fw-semibold">
                        {{ $item->product->name }}
                    </a>
                    <div class="text-muted small">
                        <i class="bi bi-tag"></i> {{ $item->product->category->name ?? '' }}
                    </div>
                </div>
            </td>
            <td>
                @if($item->product->discount_price)
                <span class="text-decoration-line-through">₹{{ $item->product->actual_price }}</span>
                <strong>₹{{ $item->product->discount_price }}</strong>
                @else
                <strong>₹{{ $item->product->actual_price }}</strong>
                @endif
            </td>
            <td>
                <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="cart_id" value="{{ $item->id }}">
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px;" onchange="this.form.submit()">
                </form>
            </td>
            <td>
                <strong>₹{{ number_format(($item->product->actual_price * $item->quantity) - (($item->product->discount_price ? ($item->product->actual_price - $item->product->discount_price) : 0) * $item->quantity), 2) }}</strong>
            </td>
            <td>
                <form action="{{ route('cart.remove') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="cart_id" value="{{ $item->id }}">
                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Subtotal</th>
            <th>₹{{ number_format($subtotal, 2) }}</th>
            <th></th>
        </tr>
        @if($totalDiscount > 0)
        <tr>
            <th colspan="3">Discount</th>
            <th class="text-success">-₹{{ number_format($totalDiscount, 2) }}</th>
            <th></th>
        </tr>
        @endif
        <tr>
            <th colspan="3">Subtotal After Discount</th>
            <th>₹{{ number_format($subtotalAfterDiscount ?? ($subtotal - $totalDiscount), 2) }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="3">Tax</th>
            <th>₹{{ number_format($totalTax, 2) }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="3">Total</th>
            <th>₹{{ number_format($total, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">Proceed to Checkout</button>
    </div>
</div>

<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payment.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="customer_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="customer_address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@else
<p>Your cart is empty.</p>
<a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
@endif
@endsection

