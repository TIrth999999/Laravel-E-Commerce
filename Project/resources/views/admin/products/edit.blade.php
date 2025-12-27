@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Product</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" value="{{ $product->name }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                @foreach($categories as $cat)
                                @php
                                    $names = [];
                                    $c = $cat;
                                    while ($c) { array_unshift($names, $c->name); $c = $c->parent; }
                                    $label = implode(' > ', $names);
                                @endphp
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Current Images</label><br>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($product->images)
                            @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" 
                                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 2px solid var(--border-color);">
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">New Images (Optional)</label>
                        <input type="file" name="images[]" class="form-control form-control-lg" accept="image/*" multiple>
                        <small class="text-muted">Select new images to replace current ones</small>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Actual Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="actual_price" class="form-control form-control-lg" step="0.01" value="{{ $product->actual_price }}" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Discount Price (₹)</label>
                            <input type="number" name="discount_price" class="form-control form-control-lg" step="0.01" value="{{ $product->discount_price }}">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Taxes</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($taxes as $tax)
                                    @php
                                        $id = 'tax_edit_' . $tax->id;
                                        $label = $tax->name . ' (' . ucfirst($tax->type) . ' - ' . $tax->value . ($tax->type == 'percentage' ? '%' : '₹') . ')';
                                    @endphp
                                    <input type="checkbox"
                                           class="btn-check"
                                           name="taxes[]"
                                           id="{{ $id }}"
                                           value="{{ $tax->id }}"
                                           {{ $product->taxes->contains($tax->id) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="{{ $id }}">{{ $label }}</label>
                                @endforeach
                            </div>
                            <small class="text-muted">Click to select/unselect taxes</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Update Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
