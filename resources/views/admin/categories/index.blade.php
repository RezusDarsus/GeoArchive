@extends('layouts.app')
@section('title', 'Manage Categories — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Administration</p><h1>Categories</h1></div><a class="button" href="{{ route('admin.categories.create') }}">Add category</a></div>
    <div class="table-wrap"><table><thead><tr><th>Image</th><th>Name</th><th>Description</th><th>Artifacts</th><th>Actions</th></tr></thead><tbody>@forelse($categories as $category)<tr><td>@if($category->image)<img class="table-thumb" src="{{ asset('storage/' . $category->image) }}" alt="">@else—@endif</td><td><strong>{{ $category->name }}</strong></td><td>{{ Str::limit($category->description, 80) ?: '—' }}</td><td>{{ $category->artifacts_count }}</td><td class="table-actions"><a href="{{ route('admin.categories.edit', $category) }}">Edit</a><form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form></td></tr>@empty<tr><td colspan="5">No categories found.</td></tr>@endforelse</tbody></table></div>
    <div class="pagination">{{ $categories->links() }}</div>
@endsection
