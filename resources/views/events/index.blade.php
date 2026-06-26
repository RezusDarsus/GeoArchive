@extends('layouts.app')
@section('title', 'Historical Events — GeoArchive')
@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Georgian timeline</p><h1>Historical events</h1><p>A chronological journey from ancient Colchis and Iberia through medieval unification, David the Builder, and modern independence.</p></div>
        <form class="filter-form archive-filters event-filters" method="GET" action="{{ route('events.index') }}">
            <label class="sr-only" for="event-q">Search events</label>
            <input id="event-q" name="q" value="{{ request('q') }}" placeholder="Search events…">
            <label class="sr-only" for="event-sort">Timeline order</label>
            <select id="event-sort" name="sort" onchange="this.form.submit()">
                <option value="oldest" @selected(request('sort', 'oldest') === 'oldest')>Oldest first</option>
                <option value="newest" @selected(request('sort') === 'newest')>Newest first</option>
            </select>
            <button class="button" type="submit">Search</button>
            @if(request()->hasAny(['q', 'sort']))<a href="{{ route('events.index') }}">Clear</a>@endif
        </form>
    </div>
    <div class="card-grid">
        @forelse($events as $event)
            @include('events._card', ['event' => $event])
        @empty
            <p class="empty-state">No historical events match your search.</p>
        @endforelse
    </div>
    <div class="pagination">{{ $events->links() }}</div>
@endsection
