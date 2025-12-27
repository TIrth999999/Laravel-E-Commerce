@php
    $padding = $level * 30;
    $borderColor = ['#e3f2fd', '#bbdefb', '#90caf9', '#64b5f6', '#42a5f5'][min($level, 4)];
@endphp

<div class="card mb-3 shadow-sm" style="margin-left: {{ $padding }}px; border-left: 4px solid {{ $borderColor }};">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                @if($category->children->count() > 0)
                <button class="btn btn-sm btn-link p-0 me-2" onclick="toggleChildren(this)" style="width: 24px; text-decoration: none; color: var(--primary-color);">
                    <i class="bi bi-chevron-down"></i>
                </button>
                @else
                <span class="me-2" style="width: 24px; display: inline-block;"></span>
                @endif
                @php
                    $catImg = $category->image;
                    $catImgUrl = \Illuminate\Support\Str::startsWith($catImg, ['http://','https://']) ? $catImg : asset('storage/' . $catImg);
                @endphp
                <img src="{{ $catImgUrl }}" alt="{{ $category->name }}" 
                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="me-3">
                <div>
                    <strong class="fs-5">{{ $category->name }}</strong>
                    @if($category->parent)
                    <small class="text-muted d-block">Parent: {{ $category->parent->name }}</small>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @if($category->children->count() > 0)
    <div class="children-container" style="display: block;">
        @foreach($category->children as $child)
            @include('admin.categories.partials.category-item', ['category' => $child, 'level' => $level + 1])
        @endforeach
    </div>
    @endif
</div>

