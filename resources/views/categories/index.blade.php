@extends('layouts.app')
@section('title', 'Categories — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Browse the archive</p><h1>Categories</h1></div></div>
    <div class="category-grid">
        @foreach($categories as $category)
            <article class="category-card">
                @if($category->image)
                    <img class="category-image" src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }} collection">
                @endif
                <div class="category-content">
                    <h2><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></h2>
                    <p>{{ $category->description ?: 'Explore artifacts in this collection.' }}</p>
                    <a href="{{ route('categories.show', $category) }}">{{ $category->artifacts_count }} {{ Str::plural('artifact', $category->artifacts_count) }} &rarr;</a>
                </div>
            </article>
        @endforeach
    </div>
@endsection
