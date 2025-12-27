@extends('layouts.admin')

@section('title', 'Taxes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-percent"></i> Taxes</h1>
    <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Tax
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxes as $tax)
                    <tr>
                        <td><strong>#{{ $tax->id }}</strong></td>
                        <td><strong>{{ $tax->name }}</strong></td>
                        <td>
                            <span class="badge {{ $tax->type == 'flat' ? 'bg-info' : 'bg-warning' }}">
                                {{ ucfirst($tax->type) }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $tax->value }}{{ $tax->type == 'percentage' ? '%' : '₹' }}</strong>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.taxes.destroy', $tax->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                            <p class="mt-3 text-muted">No taxes found. Create your first tax!</p>
                            <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Add Tax
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
