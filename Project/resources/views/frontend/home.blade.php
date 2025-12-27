@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <div class="card sidebar-card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Categories</h5>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary">All</a>
                </div>

                <div class="small text-muted mb-2">Browse by category</div>

                @foreach($categories as $category)
                    @include('frontend.partials.category-tree', ['category' => $category, 'level' => 0, 'selectedCategory' => $selectedCategory])
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Products</h1>
                <div class="text-muted">
                    @if($selectedCategory)
                        Showing products in <strong>{{ $selectedCategory->name }}</strong>
                    @else
                        Showing all products
                    @endif
                </div>
            </div>
            <div class="w-100 w-md-auto" style="max-width: 420px;">
                <input id="productSearch" type="text" class="form-control search-input" placeholder="Search products...">
            </div>
        </div>

        <div class="row g-4" id="productGrid">
            @forelse($products as $product)
                @php
                    $img = ($product->images && count($product->images) > 0) ? $product->images[0] : null;
                    $imgUrl = $img && (\Illuminate\Support\Str::startsWith($img, ['http://','https://'])) ? $img : ($img ? asset('storage/' . $img) : 'https://picsum.photos/seed/p' . $product->id . '/800/800');
                @endphp
                <div class="col-sm-6 col-xl-4 product-card" data-name="{{ strtolower($product->name) }}">
                    <div class="card h-100">
                        <img src="{{ $imgUrl }}" class="card-img-top" alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <h5 class="card-title mb-1 product-title">{{ $product->name }}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-tag"></i> {{ $product->category->name }}
                                </div>
                            </div>

                            <div class="mb-3">
                                @if($product->discount_price)
                                    <span class="text-muted text-decoration-line-through me-2">₹{{ number_format($product->actual_price, 2) }}</span>
                                    <span class="fw-bold text-primary fs-5">₹{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="badge bg-success ms-2">
                                        {{ round((($product->actual_price - $product->discount_price) / $product->actual_price) * 100) }}% OFF
                                    </span>
                                @else
                                    <span class="fw-bold text-primary fs-5">₹{{ number_format($product->actual_price, 2) }}</span>
                                @endif
                            </div>

                            <div class="mt-auto pt-2">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('product', $product->id) }}" class="btn btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-eye"></i><span>View</span>
                                </a>
                                    <form action="{{ route('cart.add') }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-cart-plus"></i><span>Add</span>
                                        </button>
                                    </form>
                                </div>
                                <div class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                            <p class="mt-3 mb-0 text-muted">No products found.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const search = document.getElementById('productSearch');
    const cards = document.querySelectorAll('.product-card');
    if (search) {
        search.addEventListener('input', function () {
            const q = (this.value || '').toLowerCase().trim();
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                card.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }
</script>
@endsection

