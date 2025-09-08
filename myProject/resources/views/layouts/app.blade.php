<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel Blog') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 0.75rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: white !important;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            padding-top: 1rem;
        }

        .sidebar h6 {
            padding: 0.5rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            background: #f9fafb;
            margin: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: #f3f4f6;
            color: var(--primary-color);
        }

        .sidebar-link.active {
            background: #eef2ff;
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .sidebar-icon {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        /* Main content */
        .main-content {
            padding: 2rem;
        }

        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-pencil-square me-2"></i>
                {{ config('app.name', 'Laravel Blog') }}
            </a>

            <div class="d-flex align-items-center ms-auto">
                @auth
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-xl-2 px-0 sidebar">
                @if(auth()->user()->role === 'admin')
                    <h6>Admin Dashboard</h6>
                    <a href="{{ route('admin.posts.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                        <i class="bi bi-file-text sidebar-icon"></i> Manage Posts
                    </a>
                    <a href="{{ route('admin.posts.create') }}"
                        class="sidebar-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle sidebar-icon"></i> Add New Post
                    </a>
                @else
                    <h6>User Dashboard</h6>
                    <a href="{{ route('user.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house sidebar-icon"></i> Dashboard
                    </a>
                    <a href="{{ route('user.submit') }}"
                        class="sidebar-link {{ request()->routeIs('user.submit') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle sidebar-icon"></i> Submit Post
                    </a>
                @endif
            </div>

            <!-- Main -->
            <div class="col-lg-9 col-xl-10 main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>