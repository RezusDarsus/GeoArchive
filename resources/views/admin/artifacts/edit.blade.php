@extends('layouts.app')
@section('title', 'Edit Artifact — GeoArchive')
@section('content')
    <div class="form-card"><p class="eyebrow">Administration</p><h1>Edit artifact</h1><form method="POST" action="{{ route('admin.artifacts.update', $artifact) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.artifacts._form', ['submitLabel' => 'Update artifact'])</form></div>
@endsection
