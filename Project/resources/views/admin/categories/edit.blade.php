@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Category</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Current Image</label><br>
                        @php
                            $catImg = $category->image;
                            $catImgUrl = \Illuminate\Support\Str::startsWith($catImg, ['http://','https://']) ? $catImg : asset('storage/' . $catImg);
                        @endphp
                        <img src="{{ $catImgUrl }}" alt="{{ $category->name }}" 
                             style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 2px solid var(--border-color);">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">New Image (Optional)</label>
                        <input type="file" name="image" class="form-control form-control-lg" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" class="form-select form-select-lg">
                            <option value="">None (Top Level Category)</option>
                            @foreach($categoriesTree as $cat)
                                @include('admin.categories.partials.parent-options', [
                                    'category' => $cat,
                                    'level' => 0,
                                    'selectedParentId' => old('parent_id', $category->parent_id)
                                ])
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Update Category
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
