@php
    $prefix = str_repeat('— ', $level);
    $hasProducts = ($category->products_count ?? 0) > 0;
    $isSelected = isset($selectedParentId) && (string)$selectedParentId !== '' && (int)$selectedParentId === (int)$category->id;
@endphp

<option value="{{ $category->id }}"
        {{ $isSelected ? 'selected' : '' }}
        {{ $hasProducts ? 'disabled' : '' }}>
    {{ $prefix }}{{ $category->name }}{{ $hasProducts ? ' (has products)' : '' }}
</option>

@if($category->children && $category->children->count() > 0)
    @foreach($category->children as $child)
        @include('admin.categories.partials.parent-options', [
            'category' => $child,
            'level' => $level + 1,
            'selectedParentId' => $selectedParentId ?? null
        ])
    @endforeach
@endif


