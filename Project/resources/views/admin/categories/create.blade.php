@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Category</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter category name" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Category Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control form-control-lg" accept="image/*" required>
                        <small class="text-muted">Recommended: Square image, min 300x300px</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" class="form-select form-select-lg">
                            <option value="">None (Top Level Category)</option>
                            @foreach($categoriesTree as $cat)
                                @include('admin.categories.partials.parent-options', [
                                    'category' => $cat,
                                    'level' => 0,
                                    'selectedParentId' => old('parent_id')
                                ])
                            @endforeach
                        </select>
                        <small class="text-muted">Select a parent category to create a subcategory</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Create Category
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
