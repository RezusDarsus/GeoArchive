@extends('layouts.app')
@section('title', $event->title . ' — Admin')
@section('content')
    <div class="page-heading"><a class="back-link" href="{{ route('admin.events.index') }}">&larr; All events</a><a class="button" href="{{ route('admin.events.edit', $event) }}">Edit event</a></div>
    <article class="detail-layout"><div>@if($event->image)<img class="detail-image" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">@else<div class="detail-placeholder event">Historical event</div>@endif</div><div>@if($event->date_or_period)<span class="badge gold">{{ $event->date_or_period }}</span>@endif<h1>{{ $event->title }}</h1>@if($event->location)<p class="location">{{ $event->location }}</p>@endif<x-long-form :text="$event->description" /></div></article>
@endsection
