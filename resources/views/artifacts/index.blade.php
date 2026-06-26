@extends('layouts.app')
@section('title', 'Artifacts — GeoArchive')
@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">The collection</p><h1>Historical artifacts</h1></div>
        <form class="filter-form archive-filters" method="GET" action="{{ route('artifacts.index') }}">
            <label class="sr-only" for="q">Search artifacts</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Search title or location…">
            <label class="sr-only" for="category">Category</label>
            <select id="category" name="category" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="tag">Tag</label>
            <select id="tag" name="tag" onchange="this.form.submit()">
                <option value="">All tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(request('tag') == $tag->id)>{{ $tag->name }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="sort">Sort</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest added</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest added</option>
            </select>
            <button class="button" type="submit">Search</button>
            @if(request()->hasAny(['q', 'category', 'tag', 'sort']))<a href="{{ route('artifacts.index') }}">Clear</a>@endif
        </form>
    </div>
    <div class="card-grid">
        @forelse($artifacts as $artifact)
            @include('artifacts._card', ['artifact' => $artifact])
        @empty
            <p class="empty-state">No artifacts match your search and filters.</p>
        @endforelse
    </div>
    <div class="pagination">{{ $artifacts->links() }}</div>
@endsection
