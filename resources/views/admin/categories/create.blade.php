@extends('layouts.app')
@section('title', 'Add Category — GeoArchive')
@section('content')<div class="form-card narrow"><p class="eyebrow">Administration</p><h1>Add category</h1><form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">@csrf @include('admin.categories._form', ['submitLabel' => 'Create category'])</form></div>@endsection
