@php
    $isSelected = $selectedCategory && $selectedCategory->id === $category->id;
    $hasChildren = $category->children && $category->children->count() > 0;
@endphp

<div class="category-item" style="padding-left: {{ $level * 14 }}px;">
    <a href="{{ route('home', ['category' => $category->id]) }}"
       class="category-link {{ $isSelected ? 'active' : '' }}">
        @if($hasChildren)
            <i class="bi bi-folder2 me-1"></i>
        @else
            <i class="bi bi-dot me-1"></i>
        @endif
        {{ $category->name }}
    </a>
</div>

@if($hasChildren)
    @foreach($category->children as $child)
        @include('frontend.partials.category-tree', ['category' => $child, 'level' => $level + 1, 'selectedCategory' => $selectedCategory])
    @endforeach
@endif


