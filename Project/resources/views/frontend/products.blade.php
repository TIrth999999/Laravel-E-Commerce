@extends('layouts.frontend')

@section('title', $category->name . ' - Products')

@section('content')
<h1>{{ $category->name }} - Products</h1>
<div class="row">
    @foreach($products as $product)
    <div class="col-md-4 mb-4">
        <div class="card">
            @if($product->images && count($product->images) > 0)
            <img src="{{ asset('storage/' . $product->images[0]) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $product->name }}</h5>
                <p class="card-text">
                    @if($product->discount_price)
                    <span class="text-decoration-line-through">₹{{ $product->actual_price }}</span>
                    <strong class="text-danger">₹{{ $product->discount_price }}</strong>
                    @else
                    <strong>₹{{ $product->actual_price }}</strong>
                    @endif
                </p>
                <a href="{{ route('product', $product->id) }}" class="btn btn-primary">View Details</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

