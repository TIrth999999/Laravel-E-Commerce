@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Category
    </a>
</div>

@foreach($categories as $category)
    @include('admin.categories.partials.category-item', ['category' => $category, 'level' => 0])
@endforeach
@endsection

@section('scripts')
<script>
    function toggleChildren(button) {
        const icon = button.querySelector('i');
        const card = button.closest('.card');
        const childrenContainer = card.querySelector('.children-container');
        
        if (childrenContainer) {
            if (childrenContainer.style.display === 'none') {
                childrenContainer.style.display = 'block';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                childrenContainer.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        }
    }
</script>
@endsection

