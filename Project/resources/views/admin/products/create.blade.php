@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Product</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter product name" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                <option value="">Select Category (Must be a leaf category)</option>
                                @foreach($categories as $cat)
                                @php
                                    $names = [];
                                    $c = $cat;
                                    while ($c) { array_unshift($names, $c->name); $c = $c->parent; }
                                    $label = implode(' > ', $names);
                                @endphp
                                <option value="{{ $cat->id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Products can only be added to categories with no subcategories</small>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Product Images <span class="text-danger">*</span></label>
                        <input type="file" name="images[]" class="form-control form-control-lg" accept="image/*" multiple required>
                        <small class="text-muted">You can select multiple images. Recommended: 800x800px or higher</small>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Actual Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="actual_price" class="form-control form-control-lg" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Discount Price (₹)</label>
                            <input type="number" name="discount_price" class="form-control form-control-lg" step="0.01" min="0" placeholder="0.00">
                            <small class="text-muted">Leave empty if no discount</small>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Taxes</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($taxes as $tax)
                                    @php
                                        $id = 'tax_create_' . $tax->id;
                                        $label = $tax->name . ' (' . ucfirst($tax->type) . ' - ' . $tax->value . ($tax->type == 'percentage' ? '%' : '₹') . ')';
                                    @endphp
                                    <input type="checkbox" class="btn-check" name="taxes[]" id="{{ $id }}" value="{{ $tax->id }}">
                                    <label class="btn btn-outline-primary btn-sm" for="{{ $id }}">{{ $label }}</label>
                                @endforeach
                            </div>
                            <small class="text-muted">Click to select/unselect taxes</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Create Product
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
