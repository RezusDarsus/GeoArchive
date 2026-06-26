@extends('layouts.app')
@section('title', 'Manage Artifacts — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Administration</p><h1>Artifacts</h1></div><a class="button" href="{{ route('admin.artifacts.create') }}">Add artifact</a></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Category</th><th>Owner</th><th>Period</th><th>Actions</th></tr></thead><tbody>
        @forelse($artifacts as $artifact)
            <tr><td><strong>{{ $artifact->title }}</strong></td><td>{{ $artifact->category->name }}</td><td>{{ $artifact->user->name }}</td><td>{{ $artifact->period ?: '—' }}</td><td class="table-actions">
                <a href="{{ route('admin.artifacts.show', $artifact) }}">View</a><a href="{{ route('admin.artifacts.edit', $artifact) }}">Edit</a>
                <form method="POST" action="{{ route('admin.artifacts.destroy', $artifact) }}" onsubmit="return confirm('Delete this artifact?')">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form>
            </td></tr>
        @empty<tr><td colspan="5">No artifacts found.</td></tr>@endforelse
    </tbody></table></div>
    <div class="pagination">{{ $artifacts->links() }}</div>
@endsection
