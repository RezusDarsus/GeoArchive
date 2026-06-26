@extends('layouts.app')
@section('title', 'Login — GeoArchive')
@section('content')
    <div class="auth-card">
        <p class="eyebrow">Welcome back</p>
        <h1>Login</h1>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required></div>
            <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="button full" type="submit">Login</button>
        </form>
        <p class="auth-foot">No account? <a href="{{ route('register') }}">Register here</a>.</p>
    </div>
@endsection
