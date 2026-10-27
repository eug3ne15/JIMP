<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Intern Portal') | Judiciary of Kenya</title>
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
            @if (auth()->user()->school)
                <span class="portal-school-badge" title="Your Academic Institution">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>{{ auth()->user()->school }}</span>
                </span>
            @endif
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
        <aside class="upper-sidebar" id="appSidebar" aria-label="Intern portal navigation">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="{{ asset('header_banner.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-full">
                    <img src="{{ asset('judiciary_icon.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-mini" title="Judiciary of Kenya">
                    <h3>Judiciary Internship Management Portal</h3>
                    <span class="sidebar-portal-tag">Intern Portal</span>
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <nav class="sidebar-nav" id="internSidebarNav" aria-label="Intern navigation">
                <a href="{{ route('dashboard.intern') }}" class="sidebar-link {{ !request()->has('tab') && !request()->routeIs('intern.courses.apply') ? 'active' : '' }}" data-tab="all-sections" title="Dashboard" data-tooltip="Dashboard">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('dashboard.intern') }}#courses-section" class="sidebar-link {{ request()->query('tab') === 'courses' ? 'active' : '' }}" data-tab="courses-section" title="Open Roles" data-tooltip="Open Roles">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="7" rx="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Open Roles</span>
                </a>
                <a href="{{ route('dashboard.intern') }}#applications-section" class="sidebar-link {{ request()->query('tab') === 'applications' ? 'active' : '' }}" data-tab="applications-section" title="My Applications" data-tooltip="My Applications">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                        <path d="m9 14 2 2 4-4"></path>
                    </svg>
                    <span>My Applications</span>
                </a>
                <a href="{{ route('dashboard.intern') }}#institution-section" class="sidebar-link {{ request()->query('tab') === 'institution' ? 'active' : '' }}" data-tab="institution-section" title="My Institution & Universities" data-tooltip="My Institution & Universities">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                        <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                    </svg>
                    <span>My Institution</span>
                </a>
            </nav>
        </aside>

        <main class="admin-content hr-content intern-portal-main">
            @include('partials.flash-notification')
            @if ($errors->any())
                <div class="hr-errors" role="alert">
                    <p><strong>Please correct the following:</strong></p>
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

    @stack('scripts')
</body>
</html>
