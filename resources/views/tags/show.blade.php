@extends('layouts.app')
@section('title', $tag->name . ' — GeoArchive')
@section('content')
    <a class="back-link" href="{{ route('artifacts.index') }}">&larr; Back to artifacts</a>
    <div class="page-heading">
        <div><p class="eyebrow">Archive tag</p><h1>{{ $tag->name }}</h1><p>Artifacts connected with {{ $tag->name }}.</p></div>
    </div>
    <div class="card-grid">
        @forelse($artifacts as $artifact)
            @include('artifacts._card', ['artifact' => $artifact])
        @empty
            <p class="empty-state">No artifacts use this tag.</p>
        @endforelse
    </div>
    <div class="pagination">{{ $artifacts->links() }}</div>
@endsection
