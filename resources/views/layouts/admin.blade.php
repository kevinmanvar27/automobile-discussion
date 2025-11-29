<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Automobile Discussion Forum</title>
    <!-- Include the custom admin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
        <!-- Mobile sidebar toggle button -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        @auth
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header justify-content-center">
                <a href="{{ route('admin.dashboard') }}" class="logo">
                    <img src="{{ asset('images/car-tech.png') }}" alt="Auto Discuss Logo" class="logo-img">
                </a>
                <!-- Close button for mobile sidebar -->
                <button class="sidebar-toggle-close" id="sidebarClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="admin-sidebar-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="menu-icon">📊</span> Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}"><span class="menu-icon">👥</span> All Users</a></li>
            </ul>
        </aside>
        @endauth
        
        @php
            $classHeader = Auth::user() ? 'admin-header' : 'd-none';
            $mainClass = Auth::user() ? 'admin-main authenticated' : 'admin-main';
        @endphp
        <main class="{{ $mainClass }}">
            @auth
                <header class=" {{ $classHeader }}">
                    <div class="admin-header-content">
                        <a href="{{ route('admin.dashboard') }}" class="admin-logo">ADMIN PANEL</a>
                        <nav class="admin-nav-links">
                            @auth
                                <a href="{{ route('home') }}">Frontend</a>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    
    <!-- Sidebar toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const adminSidebar = document.getElementById('adminSidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    adminSidebar.classList.add('active');
                });
            }
            
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    adminSidebar.classList.remove('active');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = adminSidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && adminSidebar.classList.contains('active')) {
                    adminSidebar.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>