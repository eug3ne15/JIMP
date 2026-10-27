<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Admin Panel</title>
    <script>
        if (window.innerWidth > 900 && localStorage.getItem('jimp_sidebar_closed') === '1') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-page dashboard-layout hr-layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <header class="hr-account-strip">
        <div class="hr-strip-left">
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle navigation sidebar" title="Toggle sidebar">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <span class="portal-badge">Welcome, {{ auth()->user()->name }}</span>
        </div>
        <details class="hr-profile-menu">
            <summary aria-label="Open profile and account options" title="Account options">
                <svg aria-hidden="true" viewBox="0 0 24 24" focusable="false">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4.5 21a7.5 7.5 0 0 1 15 0"></path>
                </svg>
            </summary>
            <div class="hr-profile-dropdown">
                <div class="hr-profile-details">
                    <span>Profile</span>
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hr-logout-option">Logout</button>
                </form>
            </div>
        </details>
    </header>

    <div class="dashboard-layout-body hr-layout-body">
        <aside class="upper-sidebar" id="appSidebar" aria-label="Admin portal navigation">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="{{ asset('header_banner.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-full">
                    <img src="{{ asset('judiciary_icon.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-mini" title="Judiciary of Kenya">
                    <h3>Judiciary Internship Management Portal</h3>
                    <span class="sidebar-portal-tag">Admin Portal</span>
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <nav class="sidebar-nav" aria-label="Admin navigation">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard" data-tooltip="Dashboard">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" title="Users" data-tooltip="Users">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="sidebar-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" title="Create User" data-tooltip="Create User">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    <span>Create User</span>
                </a>
            </nav>
        </aside>
        <main class="admin-content hr-content">
            @include('partials.flash-notification')
            @if ($errors->any())
                <div class="hr-errors" role="alert">
                    <p>Please correct the following:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</body>
</html>
