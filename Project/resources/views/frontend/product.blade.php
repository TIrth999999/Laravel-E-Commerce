@extends('layouts.frontend')

@section('title', $product->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        @if($product->images && count($product->images) > 0)
        <img src="{{ asset('storage/' . $product->images[0]) }}" class="img-fluid" alt="{{ $product->name }}">
        @endif
    </div>
    <div class="col-md-6">
        <h1>{{ $product->name }}</h1>
        <p><strong>Category:</strong> {{ $product->category->name }}</p>
        <p>
            @if($product->discount_price)
            <span class="text-decoration-line-through">₹{{ $product->actual_price }}</span>
            <strong class="text-danger fs-4">₹{{ $product->discount_price }}</strong>
            @else
            <strong class="fs-4">₹{{ $product->actual_price }}</strong>
            @endif
        </p>
        @if($product->taxes->count() > 0)
        <p><strong>Taxes:</strong>
            @foreach($product->taxes as $tax)
            <span class="badge bg-secondary">{{ $tax->name }} ({{ $tax->type }} - {{ $tax->value }}{{ $tax->type == 'percentage' ? '%' : '' }})</span>
            @endforeach
        </p>
        @endif
        <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Add to Cart</button>
        </form>
    </div>
</div>
@endsection

