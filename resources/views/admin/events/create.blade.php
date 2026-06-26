@extends('layouts.app')
@section('title', 'Add Event — GeoArchive')
@section('content')<div class="form-card"><p class="eyebrow">Administration</p><h1>Add historical event</h1><form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">@csrf @include('admin.events._form', ['submitLabel' => 'Create event'])</form></div>@endsection
