@extends('layouts.app')
@section('title', 'Edit Category — GeoArchive')
@section('content')<div class="form-card narrow"><p class="eyebrow">Administration</p><h1>Edit category</h1><form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.categories._form', ['submitLabel' => 'Update category'])</form></div>@endsection
