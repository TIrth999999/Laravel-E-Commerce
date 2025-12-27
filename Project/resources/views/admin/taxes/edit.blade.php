@extends('layouts.admin')

@section('title', 'Edit Tax')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Tax</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.taxes.update', $tax->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tax Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ $tax->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tax Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-lg" required>
                            <option value="flat" {{ $tax->type == 'flat' ? 'selected' : '' }}>Flat Amount (Fixed ₹)</option>
                            <option value="percentage" {{ $tax->type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Apply Tax</label>
                        <select name="apply_on" class="form-select form-select-lg" required>
                            <option value="after_discount" {{ ($tax->apply_on ?? 'after_discount') == 'after_discount' ? 'selected' : '' }}>After Discount (Recommended)</option>
                            <option value="before_discount" {{ ($tax->apply_on ?? 'after_discount') == 'before_discount' ? 'selected' : '' }}>Before Discount</option>
                        </select>
                        <small class="text-muted">Controls whether % tax is calculated on discounted price or original price</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tax Value <span class="text-danger">*</span></label>
                        <input type="number" name="value" class="form-control form-control-lg" step="0.01" min="0" value="{{ $tax->value }}" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Update Tax
                        </button>
                        <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
