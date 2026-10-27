<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | HR Portal</title>
    <script>
        if (window.innerWidth > 900 && localStorage.getItem('jimp_sidebar_closed') === '1') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-page hr-layout">
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

    <div class="hr-layout-body">
        <aside class="upper-sidebar" id="appSidebar" aria-label="HR portal navigation">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="{{ asset('header_banner.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-full">
                    <img src="{{ asset('judiciary_icon.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-mini" title="Judiciary of Kenya">
                    <h3>Judiciary Internship Management Portal</h3>
                    <span class="sidebar-portal-tag">HR Portal</span>
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <nav class="sidebar-nav" aria-label="HR navigation">
                <a href="{{ route('hr.dashboard') }}" class="sidebar-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}" title="Dashboard" data-tooltip="Dashboard">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('hr.intern.index') }}" class="sidebar-link {{ request()->routeIs('hr.intern.*') ? 'active' : '' }}" title="Interns" data-tooltip="Interns">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>Interns</span>
                </a>
                <a href="{{ route('hr.courses.index') }}" class="sidebar-link {{ request()->routeIs('hr.courses.*') ? 'active' : '' }}" title="Open Roles" data-tooltip="Open Roles">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="7" rx="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Open Roles</span>
                </a>
                <a href="{{ route('hr.universities.index') }}" class="sidebar-link {{ request()->routeIs('hr.universities.*') ? 'active' : '' }}" title="Universities" data-tooltip="Universities">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                        <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                    </svg>
                    <span>Universities</span>
                </a>
                <a href="{{ route('hr.supervisors.index') }}" class="sidebar-link {{ request()->routeIs('hr.supervisors.*') ? 'active' : '' }}" title="Supervisors" data-tooltip="Supervisors">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Supervisors</span>
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
