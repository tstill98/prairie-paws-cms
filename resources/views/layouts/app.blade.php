<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prairie Paws CMS - @yield('title', 'Home')</title>
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container flex-between">
            <h1 class="site-title">Prairie Paws CMS</h1>
            <nav>
                <ul class="nav-list">
                    <li><a href="{{ route('animals.index') }}" class="nav-link">Home</a></li>
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <li><a href="{{ route('animals.create') }}" class="nav-link">Add Animal</a></li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="nav-button">Logout</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                        <li><a href="{{ route('register') }}" class="nav-link">Register</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="container main-content">
        <!-- Success/Error messages (Flash messages) -->
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Prairie Paws CMS. All rights reserved.</p>
    </footer>
</body>
</html>
