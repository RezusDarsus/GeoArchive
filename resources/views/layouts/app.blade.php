<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GeoArchive')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <nav class="nav container" aria-label="Main navigation">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark">G</span><span>GeoArchive<small>Georgia through time</small></span></a>
            <div class="nav-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('artifacts.index') }}">Artifacts</a>
                <a href="{{ route('events.index') }}">Events</a>
                <a href="{{ route('history-paths.index') }}">History Paths</a>
                <a href="{{ route('history-graph.index') }}">History Graph</a>
                <a href="{{ route('categories.index') }}">Categories</a>
                @auth
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    @if(auth()->user()->isAdmin())
                        <a class="admin-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="link-button" type="submit">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endguest
            </div>
        </nav>
    </header>

    <main class="container page-shell">
        @if(session('success'))
            <div class="alert success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error" role="alert">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">GeoArchive &middot; Georgian Historical Artifact and Event Management System</div>
    </footer>
</body>
</html>
