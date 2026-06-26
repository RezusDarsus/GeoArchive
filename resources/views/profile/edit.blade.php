@extends('layouts.app')
@section('title', 'Edit Profile — GeoArchive')
@section('content')
    <div class="form-card narrow">
        <div class="page-heading"><div><p class="eyebrow">Your account</p><h1>Edit profile</h1><p>{{ auth()->user()->name }} &middot; {{ auth()->user()->email }}</p></div></div>
        @if($profile->avatar)
            <img class="avatar" src="{{ asset('storage/' . $profile->avatar) }}" alt="{{ auth()->user()->name }} avatar">
        @endif
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="field"><label for="bio">Bio</label><textarea id="bio" name="bio" rows="7" maxlength="1000">{{ old('bio', $profile->bio) }}</textarea><small>Maximum 1,000 characters.</small></div>
            <div class="field"><label for="avatar">Avatar image</label><input id="avatar" type="file" name="avatar" accept="image/*"><small>JPEG, PNG, GIF, BMP or WebP; maximum 2 MB.</small></div>
            @if($profile->avatar)<label class="check"><input type="checkbox" name="remove_avatar" value="1"> Remove current avatar</label>@endif
            <button class="button" type="submit">Save profile</button>
        </form>
    </div>
@endsection
