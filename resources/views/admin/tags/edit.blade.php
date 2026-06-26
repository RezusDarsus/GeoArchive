@extends('layouts.app')
@section('title', 'Edit Tag — GeoArchive')
@section('content')<div class="form-card narrow"><p class="eyebrow">Administration</p><h1>Edit tag</h1><form method="POST" action="{{ route('admin.tags.update', $tag) }}">@csrf @method('PUT') @include('admin.tags._form', ['submitLabel' => 'Update tag'])</form></div>@endsection
