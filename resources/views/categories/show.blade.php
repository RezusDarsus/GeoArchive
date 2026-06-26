@extends('layouts.app')
@section('title', $category->name . ' — GeoArchive')
@section('content')
    <a class="back-link" href="{{ route('categories.index') }}">&larr; All categories</a>
    <div class="page-heading">
        <div><p class="eyebrow">Archive category</p><h1>{{ $category->name }}</h1><p>{{ $category->description }}</p></div>
    </div>
    <div class="card-grid">
        @forelse($artifacts as $artifact)
            @include('artifacts._card', ['artifact' => $artifact])
        @empty
            <p class="empty-state">No artifacts are assigned to this category.</p>
        @endforelse
    </div>
    <div class="pagination">{{ $artifacts->links() }}</div>
@endsection
