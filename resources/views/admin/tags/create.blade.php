@extends('layouts.app')
@section('title', 'Add Tag — GeoArchive')
@section('content')<div class="form-card narrow"><p class="eyebrow">Administration</p><h1>Add tag</h1><form method="POST" action="{{ route('admin.tags.store') }}">@csrf @include('admin.tags._form', ['submitLabel' => 'Create tag'])</form></div>@endsection
