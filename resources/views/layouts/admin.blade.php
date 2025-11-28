<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Automobile Discussion Forum</title>
    <!-- Include the custom admin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <style>
        /* Additional styles that need to be inline */
        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }     
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo-img {
            height: 120px;
            width: auto;
        }
        
    </style>
</head>
<body>
    <div class="admin-container">
        @auth
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="logo">
                    <img src="{{ asset('images/car-tech.png') }}" alt="Auto Discuss Logo" class="logo-img">
                </a>
            </div>
            <ul class="admin-sidebar-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="menu-icon">📊</span> Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}"><span class="menu-icon">👥</span> All Users</a></li>
            </ul>
        </aside>
        @endauth
        
        @php
            $classHeader = Auth::user() ? 'admin-header' : 'd-none';
            $classMainCss = Auth::user() ? 'margin-left:250px;' : 'margin-left:0px;';
        @endphp
        <main class="admin-main" style="<?php echo $classMainCss; ?>">
            @auth
                <header class=" {{ $classHeader }}">
                    <div class="admin-header-content">
                        <a href="{{ route('admin.dashboard') }}" class="admin-logo">ADMIN PANEL</a>
                        <nav class="admin-nav-links">
                            @auth
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                <a href="{{ route('logout') }}" 
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('admin.login') }}">Login</a>
                            @endauth    
                        </nav>
                    </div>
                </header>
            @endauth

            @if(session('success'))
                <div class="admin-alert admin-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="admin-alert admin-alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('admin-content')
        </main>
    </div>
    
    <!-- Hidden logout form for admin panel -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>