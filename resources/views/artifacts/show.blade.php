@extends('layouts.app')
@section('title', $artifact->title . ' — GeoArchive')
@section('content')
    <a class="back-link" href="{{ route('artifacts.index') }}">&larr; Back to artifacts</a>
    <article class="detail-layout">
        <div>
            @if($artifact->image)
                <img class="detail-image" src="{{ asset('storage/' . $artifact->image) }}" alt="{{ $artifact->title }}">
            @else
                <div class="detail-placeholder">Historical artifact</div>
            @endif
        </div>
        <div>
            <a class="badge" href="{{ route('categories.show', $artifact->category) }}">{{ $artifact->category->name }}</a>
            <h1>{{ $artifact->title }}</h1>
            <dl class="metadata">
                @if($artifact->period)<div><dt>Period</dt><dd>{{ $artifact->period }}</dd></div>@endif
                @if($artifact->location)<div><dt>Location</dt><dd>{{ $artifact->location }}</dd></div>@endif
                <div><dt>Added by</dt><dd>{{ $artifact->user->name }}</dd></div>
            </dl>
            <x-long-form :text="$artifact->description" />
            @if($artifact->tags->isNotEmpty())
                <div class="tags">@foreach($artifact->tags as $tag)<a href="{{ route('tags.show', $tag) }}">{{ $tag->name }}</a>@endforeach</div>
            @endif
            @if($artifact->historicalEvents->isNotEmpty())
                <aside class="related-archive">
                    <p class="eyebrow">Connected history</p>
                    <h2>Events connected to this artifact</h2>
                    <div class="related-links">
                        @foreach($artifact->historicalEvents as $event)
                            <a href="{{ route('events.show', $event) }}">{{ $event->title }} <span>→</span></a>
                        @endforeach
                    </div>
                </aside>
            @endif
        </div>
    </article>
@endsection
