@extends('layouts.app')
@section('title', 'Edit Event — GeoArchive')
@section('content')<div class="form-card"><p class="eyebrow">Administration</p><h1>Edit historical event</h1><form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.events._form', ['submitLabel' => 'Update event'])</form></div>@endsection
