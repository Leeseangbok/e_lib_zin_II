<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.books') }}">Books</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <div class="row">
            <aside class="col-md-2 bg-light sidebar py-4">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.books') }}">Manage Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Settings</a></li>
                </ul>
            </aside>
            <main class="col-md-10">
                @yield('content')
            </main>
        </div>
    </div>
    <footer class="footer bg-dark text-white text-center py-3 mt-4">
        &copy; {{ date('Y') }} e-Library Admin Panel
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
