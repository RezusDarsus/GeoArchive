@extends('layouts.app')
@section('title', 'Register — GeoArchive')
@section('content')
    <div class="auth-card">
        <p class="eyebrow">Join the archive</p>
        <h1>Create account</h1>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="field"><label for="name">Name</label><input id="name" name="name" value="{{ old('name') }}" required autofocus></div>
            <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required></div>
            <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required></div>
            <button class="button full" type="submit">Register</button>
        </form>
        <p class="auth-foot">Already registered? <a href="{{ route('login') }}">Login</a>.</p>
    </div>
@endsection
