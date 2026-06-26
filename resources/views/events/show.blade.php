@extends('layouts.app')
@section('title', $event->title . ' — GeoArchive')
@section('content')
    <a class="back-link" href="{{ route('events.index') }}">&larr; Back to events</a>
    <article class="detail-layout">
        <div>
            @if($event->image)<img class="detail-image" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
            @else<div class="detail-placeholder event">Historical event</div>@endif
        </div>
        <div>
            @if($event->date_or_period)<span class="badge gold">{{ $event->date_or_period }}</span>@endif
            <h1>{{ $event->title }}</h1>
            @if($event->location)<p class="location">{{ $event->location }}</p>@endif
            <x-long-form :text="$event->description" />
            @if($event->artifacts->isNotEmpty())
                <aside class="related-archive">
                    <p class="eyebrow">Connected evidence</p>
                    <h2>Artifacts connected to this event</h2>
                    <div class="related-links">
                        @foreach($event->artifacts as $artifact)
                            <a href="{{ route('artifacts.show', $artifact) }}">{{ $artifact->title }} <span>→</span></a>
                        @endforeach
                    </div>
                </aside>
            @endif
        </div>
    </article>
@endsection
