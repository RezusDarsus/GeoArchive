@extends('layouts.app')
@section('title', 'Add Artifact — GeoArchive')
@section('content')
    <div class="form-card"><p class="eyebrow">Administration</p><h1>Add artifact</h1><form method="POST" action="{{ route('admin.artifacts.store') }}" enctype="multipart/form-data">@csrf @include('admin.artifacts._form', ['submitLabel' => 'Create artifact'])</form></div>
@endsection
