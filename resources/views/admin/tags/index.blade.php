@extends('layouts.app')
@section('title', 'Manage Tags — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Administration</p><h1>Tags</h1></div><a class="button" href="{{ route('admin.tags.create') }}">Add tag</a></div>
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Artifacts</th><th>Actions</th></tr></thead><tbody>@forelse($tags as $tag)<tr><td><strong>{{ $tag->name }}</strong></td><td>{{ $tag->artifacts_count }}</td><td class="table-actions"><a href="{{ route('admin.tags.edit', $tag) }}">Edit</a><form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag?')">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form></td></tr>@empty<tr><td colspan="3">No tags found.</td></tr>@endforelse</tbody></table></div>
    <div class="pagination">{{ $tags->links() }}</div>
@endsection
