@extends('layouts.frontend')

@section('title', $category->name)

@section('content')
<h1>{{ $category->name }}</h1>
@if($category->children->count() > 0)
<h3>Subcategories</h3>
<div class="row">
    @foreach($category->children as $subcategory)
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="{{ asset('storage/' . $subcategory->image) }}" class="card-img-top" alt="{{ $subcategory->name }}" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">{{ $subcategory->name }}</h5>
                <a href="{{ route('category', $subcategory->id) }}" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<h3>Products</h3>
<div class="row">
    @foreach($category->products as $product)
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
@endif
@endsection

