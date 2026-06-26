@extends('layouts.app')

@section('title', 'GeoArchive — Georgian History')

@section('content')
    <section class="hero">
        <p class="eyebrow">Preserving Georgian history</p>
        <h1>GeoArchive</h1>
        <p>Explore artifacts and defining events from Georgia's ancient kingdoms, medieval golden age, and modern history.</p>
        <div class="actions">
            <a class="button" href="{{ route('artifacts.index') }}">Browse artifacts</a>
            <a class="button secondary" href="{{ route('events.index') }}">Explore events</a>
        </div>
    </section>

    <section class="section-heading">
        <div><p class="eyebrow">Collection</p><h2>Latest artifacts</h2></div>
        <a href="{{ route('artifacts.index') }}">View all &rarr;</a>
    </section>
    <div class="card-grid">
        @forelse($artifacts as $artifact)
            @include('artifacts._card', ['artifact' => $artifact])
        @empty
            <p class="empty-state">No artifacts have been added yet.</p>
        @endforelse
    </div>

    <section class="section-heading">
        <div><p class="eyebrow">Timeline</p><h2>Latest historical events</h2></div>
        <a href="{{ route('events.index') }}">View all &rarr;</a>
    </section>
    <div class="card-grid">
        @forelse($events as $event)
            @include('events._card', ['event' => $event])
        @empty
            <p class="empty-state">No historical events have been added yet.</p>
        @endforelse
    </div>
@endsection
