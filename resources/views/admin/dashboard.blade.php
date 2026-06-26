@extends('layouts.app')
@section('title', 'Admin Dashboard — GeoArchive')
@section('content')
    <div class="page-heading"><div><p class="eyebrow">Administration</p><h1>Dashboard</h1><p>Manage the GeoArchive collection and historical timeline.</p></div></div>
    <div class="stat-grid">
        @foreach($counts as $label => $count)
            <div class="stat-card"><strong>{{ $count }}</strong><span>Total {{ strtolower($label) }}</span></div>
        @endforeach
    </div>
    <h2>Quick links</h2>
    <div class="quick-grid">
        <a href="{{ route('admin.artifacts.index') }}"><strong>Manage Artifacts</strong><span>Create, update, tag, and upload images.</span></a>
        <a href="{{ route('admin.categories.index') }}"><strong>Manage Categories</strong><span>Organize the collection.</span></a>
        <a href="{{ route('admin.tags.index') }}"><strong>Manage Tags</strong><span>Maintain searchable context.</span></a>
        <a href="{{ route('admin.events.index') }}"><strong>Manage Historical Events</strong><span>Edit the public timeline.</span></a>
    </div>
@endsection
