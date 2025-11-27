<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Automobile Discussion Forum')</title>
    <style>
        /* Light Theme */
        :root {
            --primary: #FF6B00; /* Orange */
            --secondary: #F5F5F5; /* Light Gray */
            --background: #FFFFFF; /* White */
            --surface: #FFFFFF; /* White */
            --text: #333333; /* Dark Gray */
            --text-secondary: #757575; /* Medium Gray */
            --border: #E0E0E0; /* Light Gray Border */
            --success: #4CAF50; /* Green */
            --error: #F44336; /* Red */
            --warning: #FF9800; /* Orange */
            
            /* Grayscale System */
            --gray-50: #FAFAFA; /* Lightest Gray */
            --gray-100: #F5F5F5;
            --gray-200: #EEEEEE;
            --gray-300: #E0E0E0;
            --gray-400: #BDBDBD;
            --gray-500: #9E9E9E;
            --gray-600: #757575;
            --gray-700: #616161;
            --gray-800: #424242;
            --gray-900: #212121; /* Darkest Gray */
            
            /* Typography */
            --font-size-xs: 12px;
            --font-size-sm: 14px;
            --font-size-md: 16px;
            --font-size-lg: 18px;
            --font-size-xl: 20px;
            --font-size-xxl: 24px;
            --font-size-xxxl: 32px;
            
            /* Spacing System */
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-xxl: 48px;
            
            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--background);
            color: var(--text);
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-md);
        }
        
        .header {
            background-color: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: var(--spacing-md) 0;
            min-height: calc(32px + var(--spacing-md));
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            width: 120px;
            height: 40px;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: var(--radius-md);
        }
        
        .nav-links a {
            margin-left: var(--spacing-md);
            text-decoration: none;
            color: var(--text);
        }
        
        .btn {
            display: inline-block;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-lg);
            text-decoration: none;
            font-size: var(--font-size-md);
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: var(--text);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-sm {
            padding: var(--spacing-sm) var(--spacing-md);
            font-size: var(--font-size-sm);
        }
        
        .card {
            background-color: var(--surface);
            border-radius: var(--radius-md);
            box-shadow: 0 2px 2.62px rgba(0, 0, 0, 0.23);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
        }
        
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        .form-control {
            width: 100%;
            padding: var(--spacing-md);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            min-height: 48px;
            font-size: var(--font-size-md);
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border: 2px solid var(--primary);
        }
        
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
        }
        
        .alert-success {
            background-color: var(--success);
            color: white;
        }
        
        .alert-error {
            background-color: var(--error);
            color: white;
        }
        
        .thread-list {
            list-style: none;
            padding: 0;
        }
        
        .thread-item {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border);
        }
        
        .thread-title {
            font-size: var(--font-size-lg);
            font-weight: 600;
            margin: 0 0 var(--spacing-sm);
        }
        
        .thread-meta {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
        
        .comment {
            border-left: 3px solid var(--primary);
            padding-left: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }
        
        .sidebar {
            background-color: var(--gray-100);
            padding: 0;
            border-radius: var(--radius-md);
            height: fit-content;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: var(--spacing-md);
            background: linear-gradient(to right, var(--primary), #FF8C42);
            color: white;
            font-weight: bold;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid var(--gray-300);
        }
        
        .sidebar-menu li:last-child {
            border-bottom: none;
        }
        
        .sidebar-menu a {
            text-decoration: none;
            color: var(--text);
            display: block;
            padding: var(--spacing-md);
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover {
            background-color: var(--gray-200);
            color: var(--primary);
        }
        
        .sidebar-menu a.active {
            background-color: var(--primary);
            color: white;
        }
        
        .sidebar-menu a.active:hover {
            background-color: #E55E00;
        }
        
        .menu-icon {
            margin-right: var(--spacing-sm);
        }
        
        footer {
            background-color: var(--gray-100);
            padding: var(--spacing-lg);
            text-align: center;
            margin-top: var(--spacing-xl);
            border-top: 1px solid var(--border);
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">@yield('logo', 'AUTO DISCUSS')</div>
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

    <main class="container">
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
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} Automobile Discussion Forum. All rights reserved.</p>
        </div>
    </footer>
    
    @yield('modals')
    @yield('scripts')
</body>
</html>