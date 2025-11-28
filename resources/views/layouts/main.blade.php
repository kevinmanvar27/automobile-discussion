<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Automobile Discussion Forum')</title>
    <!-- Include the custom frontend CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">

    <style>
        /* Additional styles that need to be inline */
        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .header {
            background-color: #ffffff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo-img {
            height: 50px;
            width: auto;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-links a {
            color: #495057;
            text-decoration: none;
            margin-left: 1.2rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
        }
        
        .nav-links a:hover {
            color: #FF6B00;
            background-color: rgba(255, 107, 0, 0.1);
        }
        
        .main-content {
            min-height: calc(100vh - 200px);
        }
        
        footer {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            padding: 1.5rem 0;
            margin-top: 3rem;
            text-align: center;
            color: #6c757d;
        }
        
        /* Alert styles */
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Card styles */
        .card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #e9ecef;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .card-title {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/car-tech.png') }}" alt="Auto Discuss Logo" class="logo-img">
            </a>
            <nav class="nav-links">
                @auth
                    @if(Auth::user()->email === 'rektech.uk@gmail.com')
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                    <a href="{{ route('discussion.index') }}">Discussion</a>
                    @yield('header-nav-items')
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}">User Login</a>
                    <a href="{{ route('admin.login') }}">Admin Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container d-flex align-items-center justify-content-center">
        <div class="w-100">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Automobile Discussion Forum. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Hidden logout form for frontend -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    @yield('modals')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    @yield('scripts')
</body>
</html>