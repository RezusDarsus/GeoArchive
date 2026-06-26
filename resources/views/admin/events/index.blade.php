@extends('layouts.app')
@section('title', 'Manage Events — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Administration</p><h1>Historical events</h1></div><a class="button" href="{{ route('admin.events.create') }}">Add event</a></div>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Date or period</th><th>Location</th><th>Actions</th></tr></thead><tbody>@forelse($events as $event)<tr><td><strong>{{ $event->title }}</strong></td><td>{{ $event->date_or_period ?: '—' }}</td><td>{{ $event->location ?: '—' }}</td><td class="table-actions"><a href="{{ route('admin.events.show', $event) }}">View</a><a href="{{ route('admin.events.edit', $event) }}">Edit</a><form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this historical event?')">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form></td></tr>@empty<tr><td colspan="4">No events found.</td></tr>@endforelse</tbody></table></div>
    <div class="pagination">{{ $events->links() }}</div>
@endsection
