<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard | Judiciary of Kenya</title>
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
        <aside class="upper-sidebar" id="appSidebar" aria-label="Supervisor portal navigation">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="{{ asset('header_banner.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-full">
                    <img src="{{ asset('judiciary_icon.png') }}" alt="Judiciary of Kenya" class="sidebar-brand-mini" title="Judiciary of Kenya">
                    <h3>Judiciary Internship Management Portal</h3>
                    <span class="sidebar-portal-tag">Supervisor Portal</span>
                </div>
                <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <nav class="sidebar-nav" aria-label="Supervisor navigation">
                <a href="#overview-section" class="sidebar-link {{ $activeTab === 'overview-section' ? 'active' : '' }}" data-tab-target="overview-section" title="Dashboard Overview" data-tooltip="Overview">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="#interns-section" class="sidebar-link {{ $activeTab === 'interns-section' ? 'active' : '' }}" data-tab-target="interns-section" title="Assigned Interns" data-tooltip="Assigned Interns">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Assigned Interns</span>
                    @if ($totalAssigned > 0)
                        <span class="intern-tab-count" style="margin-left: auto; background: var(--judiciary-gold, #d5a62e); color: #025532; font-weight: 700; font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px;">
                            {{ $totalAssigned }}
                        </span>
                    @endif
                </a>
            </nav>
        </aside>

        <main class="admin-content hr-content">
            @include('partials.flash-notification')
            @if ($errors->any())
                <ul class="admin-errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- TAB 1: Supervisor Statistics Overview --}}
            <div id="overview-section" class="supervisor-tab-pane {{ $activeTab === 'overview-section' ? 'active' : '' }}">
                <div class="hr-stats-overview-grid" style="margin-bottom: 1.5rem;">
                    {{-- Metric 1: Assigned Interns --}}
                    <div class="hr-stat-metric-card" style="cursor: pointer;" data-tab-target="interns-section" title="Click to view assigned interns">
                        <div class="metric-icon-wrap metric-fields-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="metric-body">
                            <span class="metric-number">{{ $totalAssigned }}</span>
                            <span class="metric-label">Assigned Interns</span>
                            <span class="metric-subtext">{{ $totalAssigned > 0 ? 'Delegated to your supervision' : 'No interns currently assigned' }}</span>
                        </div>
                    </div>

                    {{-- Metric 2: Active Placements --}}
                    <div class="hr-stat-metric-card">
                        <div class="metric-icon-wrap metric-placed-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                                <rect width="20" height="14" x="2" y="7" rx="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <div class="metric-body">
                            <span class="metric-number">{{ $activePlacements }}</span>
                            <span class="metric-label">Active Placements</span>
                            <span class="metric-subtext">Approved &amp; active cohorts</span>
                        </div>
                    </div>

                    {{-- Metric 3: Reports Submitted --}}
                    <div class="hr-stat-metric-card">
                        <div class="metric-icon-wrap metric-apps-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="m9 15 2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="metric-body">
                            <span class="metric-number">{{ $reportsSubmitted }}</span>
                            <span class="metric-label">Reports Submitted</span>
                            <span class="metric-subtext">Evaluations on file</span>
                        </div>
                    </div>

                    {{-- Metric 4: Pending Reports --}}
                    <div class="hr-stat-metric-card">
                        <div class="metric-icon-wrap metric-dept-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="metric-body">
                            <span class="metric-number">{{ $reportsPending }}</span>
                            <span class="metric-label">Pending Reports</span>
                            <span class="metric-subtext">{{ $reportsPending === 0 ? 'All evaluations up-to-date' : 'Awaiting review upload' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Overview Quick Look Section --}}
                <div class="admin-panel" style="margin-top: 1rem;">
                    <div class="admin-panel-heading">
                        <div>
                            <h2>Assigned Interns Overview</h2>
                            <p>{{ $totalAssigned }} {{ $totalAssigned === 1 ? 'intern' : 'interns' }} assigned to you</p>
                        </div>
                        <button type="button" class="btn-supervisor-view-details" data-tab-target="interns-section" style="padding: 0.5rem 1rem;">
                            <span>View All Assigned Interns &rarr;</span>
                        </button>
                    </div>
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Intern</th>
                                    <th>Internship & Directorate</th>
                                    <th>University / Course</th>
                                    <th>Evaluation Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications->take(5) as $application)
                                    <tr>
                                        <td data-label="Intern">
                                            <div class="sup-intern-cell">
                                                <div class="sup-intern-avatar">
                                                    {{ strtoupper(substr($application->applicant_name ?: $application->user?->name ?: 'IN', 0, 2)) }}
                                                </div>
                                                <div class="sup-intern-info">
                                                    <div class="sup-intern-name">
                                                        {{ $application->applicant_name ?: $application->user?->name ?: 'Intern' }}
                                                    </div>
                                                    <span class="sup-intern-email">
                                                        {{ $application->user?->email ?? '—' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Internship">
                                            <div style="font-weight: 600; color: #025532;">
                                                {{ $application->course?->name ?? $application->internship?->course?->name ?? $application->internship?->title ?? 'Commercial Law Intern' }}
                                            </div>
                                            @if ($application->course?->department)
                                                <span style="display: block; font-size: 0.76rem; color: #64748b; margin-top: 2px;">
                                                    🏛️ {{ $application->course->department->name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td data-label="University / Course">
                                            <div style="color: #334155; font-weight: 500;">
                                                {{ $application->institution ?: $application->user?->school ?: 'University not provided' }}
                                            </div>
                                            @if ($application->field_of_study)
                                                <span class="badge-field-study" style="font-size: 0.76rem; padding: 0.15rem 0.5rem; background: #ecfdf5; color: #047857; border-radius: 999px; border: 1px solid #a7f3d0; display: inline-block; margin-top: 3px;">
                                                    🎓 {{ $application->field_of_study }}
                                                </span>
                                            @endif
                                        </td>
                                        <td data-label="Evaluation Status">
                                            @if ($application->supervisor_evaluation_path)
                                                <span class="sup-eval-pill pill-submitted">
                                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    Report submitted
                                                </span>
                                            @else
                                                <span class="sup-eval-pill pill-pending">
                                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                    Awaiting report
                                                </span>
                                            @endif
                                        </td>
                                        <td data-label="Action">
                                            <button type="button" class="btn-supervisor-view-details" data-modal-target="internDetailsModal-{{ $application->id }}">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <span>View Details</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="admin-empty" colspan="5">No interns are currently assigned to you.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Full Assigned Interns Directory --}}
            <section class="admin-panel supervisor-tab-pane {{ $activeTab === 'interns-section' ? 'active' : '' }}" id="interns-section">
                <div class="sup-directory-header">
                    <div class="sup-dir-title-area">
                        <h2>
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" style="color: #025532;">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Assigned interns</span>
                        </h2>
                        <p>{{ $totalAssigned }} {{ $totalAssigned === 1 ? 'intern' : 'interns' }} assigned to you &mdash; track academic progress, view documents, and submit evaluations</p>
                    </div>
                    <div class="sup-search-box">
                        <svg class="sup-search-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="internSearchInput" class="sup-search-input" placeholder="Search by intern name, role, university..." aria-label="Search interns">
                    </div>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table" id="assignedInternsTable">
                        <thead>
                            <tr>
                                <th>Intern</th>
                                <th>Internship & Directorate</th>
                                <th>University / Discipline</th>
                                <th>Evaluation report</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                <tr class="intern-row-item" data-search-text="{{ strtolower(($application->applicant_name ?: $application->user?->name ?: '') . ' ' . ($application->user?->email ?? '') . ' ' . ($application->course?->name ?? '') . ' ' . ($application->institution ?: $application->user?->school ?: '') . ' ' . ($application->field_of_study ?? '')) }}">
                                    <td data-label="Intern">
                                        <div class="sup-intern-cell">
                                            <div class="sup-intern-avatar">
                                                {{ strtoupper(substr($application->applicant_name ?: $application->user?->name ?: 'IN', 0, 2)) }}
                                            </div>
                                            <div class="sup-intern-info">
                                                <div class="sup-intern-name">
                                                    {{ $application->applicant_name ?: $application->user?->name ?: 'Intern' }}
                                                </div>
                                                <span class="sup-intern-email">
                                                    {{ $application->user?->email ?? '—' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Internship">
                                        <div style="font-weight: 600; color: #025532;">
                                            {{ $application->course?->name ?? $application->internship?->course?->name ?? $application->internship?->title ?? 'Registry Administration' }}
                                        </div>
                                        @if ($application->course?->department)
                                            <span style="display: block; font-size: 0.76rem; color: #64748b; margin-top: 2px;">
                                                🏛️ {{ $application->course->department->name }}
                                            </span>
                                        @endif
                                        @if ($application->internship?->start_date && $application->internship?->end_date)
                                            <span style="display: block; font-size: 0.74rem; color: #94a3b8; margin-top: 2px;">
                                                📅 {{ $application->internship->start_date->format('M Y') }} — {{ $application->internship->end_date->format('M Y') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="University / Course">
                                        <div style="color: #334155; font-weight: 500;">
                                            {{ $application->institution ?: $application->user?->school ?: 'University not provided' }}
                                        </div>
                                        <span class="hr-table-subtitle" style="display: inline-block; margin-top: 3px;">
                                            @if ($application->field_of_study)
                                                <span class="badge-field-study" style="font-size: 0.76rem; padding: 0.15rem 0.5rem; background: #ecfdf5; color: #047857; border-radius: 999px; border: 1px solid #a7f3d0;">
                                                    🎓 {{ $application->field_of_study }}
                                                </span>
                                            @elseif ($application->user?->course)
                                                <span style="font-size: 0.78rem; color: #64748b;">{{ $application->user->course }}</span>
                                            @else
                                                <span style="font-size: 0.78rem; color: #94a3b8;">Course not provided</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td data-label="Evaluation report">
                                        @if ($application->supervisor_evaluation_path)
                                            <div style="display: inline-flex; align-items: center; gap: 0.35rem; color: #15803d; font-weight: 600; font-size: 0.82rem; margin-bottom: 0.4rem;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Report submitted</span>
                                            </div>
                                        @else
                                            <div style="display: inline-flex; align-items: center; gap: 0.35rem; color: #b45309; font-weight: 600; font-size: 0.82rem; margin-bottom: 0.4rem;">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <span>Awaiting report</span>
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('supervisor.applications.evaluation.store', $application) }}" enctype="multipart/form-data" class="admin-actions" style="margin-top: 0.2rem;">
                                            @csrf
                                            <input type="file" name="evaluation" accept=".pdf,.doc,.docx" required aria-label="Evaluation report for {{ $application->applicant_name ?: $application->user?->name ?: 'Intern' }}">
                                            <button class="admin-submit" type="submit">{{ $application->supervisor_evaluation_path ? 'Replace report' : 'Submit report' }}</button>
                                        </form>
                                    </td>
                                    <td data-label="Actions" style="text-align: right;">
                                        <div class="sup-actions-wrap" style="justify-content: flex-end;">
                                            <button type="button" class="btn-supervisor-view-details" data-modal-target="internDetailsModal-{{ $application->id }}" title="View full profile and submitted documents">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <span>View Details</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="admin-empty" colspan="5">No interns are currently assigned to you.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">{{ $applications->links() }}</div>
            </section>
        </main>
    </div>

    {{-- Interactive Intern Details Modals for each assigned intern --}}
    @foreach ($applications as $application)
        <div id="internDetailsModal-{{ $application->id }}" class="sup-details-modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="modalTitle-{{ $application->id }}">
            <div class="sup-modal-backdrop" data-modal-close="internDetailsModal-{{ $application->id }}"></div>
            <div class="sup-modal-card">
                <div class="sup-modal-header">
                    <div class="sup-modal-profile">
                        <div class="sup-modal-avatar">
                            {{ strtoupper(substr($application->applicant_name ?: $application->user?->name ?: 'IN', 0, 2)) }}
                        </div>
                        <div>
                            <h3 id="modalTitle-{{ $application->id }}" class="sup-modal-name">{{ $application->applicant_name ?: $application->user?->name ?: 'Intern Candidate' }}</h3>
                            <p class="sup-modal-sub">{{ $application->user?->email ?? 'No email provided' }} &bull; Placement ID #{{ $application->id }}</p>
                        </div>
                    </div>
                    <button type="button" class="sup-modal-close-btn" data-modal-close="internDetailsModal-{{ $application->id }}" aria-label="Close modal">&times;</button>
                </div>

                <div class="sup-modal-content">
                    {{-- 1. Directorate & Role Placement --}}
                    <div class="sup-modal-box">
                        <h4 class="sup-modal-box-title">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Internship Placement & Directorate
                        </h4>
                        <div class="sup-grid-2">
                            <div class="sup-data-item">
                                <label>Department / Directorate</label>
                                <strong>🏛️ {{ $application->course?->department->name ?? 'Judiciary Directorate' }}</strong>
                            </div>
                            <div class="sup-data-item">
                                <label>Internship Position / Role</label>
                                <strong style="color: #025532;">{{ $application->course?->name ?? $application->internship?->course?->name ?? 'Registry Administration' }}</strong>
                            </div>
                            <div class="sup-data-item">
                                <label>Supervising Officer</label>
                                <strong>{{ auth()->user()->name }}</strong>
                            </div>
                            <div class="sup-data-item">
                                <label>Placement Duration</label>
                                @if ($application->internship?->start_date && $application->internship?->end_date)
                                    <strong>{{ $application->internship->start_date->format('M d, Y') }} &mdash; {{ $application->internship->end_date->format('M d, Y') }}</strong>
                                @else
                                    <strong style="color: #64748b;">Current Active Cohort</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Academic Background & Discipline --}}
                    <div class="sup-modal-box">
                        <h4 class="sup-modal-box-title">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            Academic Qualifications & Institution
                        </h4>
                        <div class="sup-grid-2">
                            <div class="sup-data-item">
                                <label>Higher Learning Institution</label>
                                <strong>{{ $application->institution ?: $application->user?->school ?: 'University not specified' }}</strong>
                            </div>
                            <div class="sup-data-item">
                                <label>Field of Study / Discipline</label>
                                @if ($application->field_of_study)
                                    <span class="badge-field-study" style="display: inline-block; margin-top: 2px;">
                                        🎓 {{ $application->field_of_study }}
                                    </span>
                                @else
                                    <span style="color: #64748b; font-size: 0.85rem;">General Academic Program</span>
                                @endif
                            </div>
                            <div class="sup-data-item">
                                <label>Degree / Course of Study</label>
                                <strong>{{ $application->user?->course ?? 'Not provided' }}</strong>
                            </div>
                            <div class="sup-data-item">
                                <label>Application Date</label>
                                <strong>{{ $application->created_at->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Supporting Documents & Credentials --}}
                    <div class="sup-modal-box">
                        <h4 class="sup-modal-box-title">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Submitted Credentials & Documents
                        </h4>
                        <div class="sup-docs-stack">
                            {{-- CV --}}
                            <div class="sup-doc-row">
                                <div class="sup-doc-meta">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" style="color: #025532;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                    <div>
                                        <div class="sup-doc-name">Curriculum Vitae / Application Letter</div>
                                        <small style="color: #64748b;">Candidate resume and cover details</small>
                                    </div>
                                </div>
                                @if ($application->cv_path)
                                    <a href="{{ route('supervisor.applications.document', [$application, 'cv']) }}" class="sup-btn-download">
                                        <span>Download CV &darr;</span>
                                    </a>
                                @else
                                    <span class="sup-doc-none">Not uploaded</span>
                                @endif
                            </div>

                            {{-- School Recommendation --}}
                            <div class="sup-doc-row">
                                <div class="sup-doc-meta">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" style="color: #025532;">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                    <div>
                                        <div class="sup-doc-name">University Recommendation Letter</div>
                                        <small style="color: #64748b;">Official institutional endorsement</small>
                                    </div>
                                </div>
                                @if ($application->school_recommendation_path)
                                    <a href="{{ route('supervisor.applications.document', [$application, 'school_recommendation']) }}" class="sup-btn-download">
                                        <span>Download Letter &darr;</span>
                                    </a>
                                @else
                                    <span class="sup-doc-none">Not uploaded</span>
                                @endif
                            </div>

                            {{-- Insurance Cover --}}
                            <div class="sup-doc-row">
                                <div class="sup-doc-meta">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" style="color: #025532;">
                                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>
                                    <div>
                                        <div class="sup-doc-name">Medical / Insurance Cover</div>
                                        <small style="color: #64748b;">Personal accident / health coverage certificate</small>
                                    </div>
                                </div>
                                @if ($application->insurance_path)
                                    <a href="{{ route('supervisor.applications.document', [$application, 'insurance']) }}" class="sup-btn-download">
                                        <span>Download Cover &darr;</span>
                                    </a>
                                @else
                                    <span class="sup-doc-none">Not uploaded</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. Supervisor Evaluation Report Section --}}
                    <div class="sup-modal-box sup-eval-box">
                        <h4 class="sup-modal-box-title">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="m9 15 2 2 4-4"></path>
                            </svg>
                            Supervisor Evaluation & Performance Review
                        </h4>

                        <div class="sup-eval-status-row">
                            @if ($application->supervisor_evaluation_path)
                                <div class="sup-eval-pill pill-submitted">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Evaluation Report on file</span>
                                </div>
                                <a href="{{ route('supervisor.applications.evaluation.download', $application) }}" class="sup-btn-download">
                                    Download Evaluation Report &darr;
                                </a>
                            @else
                                <div class="sup-eval-pill pill-pending">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span>Pending Evaluation Submission</span>
                                </div>
                            @endif
                        </div>

                        <div class="sup-eval-form-box">
                            <form method="POST" action="{{ route('supervisor.applications.evaluation.store', $application) }}" enctype="multipart/form-data">
                                @csrf
                                <label for="modal-eval-file-{{ $application->id }}">
                                    {{ $application->supervisor_evaluation_path ? 'Upload revised evaluation report (PDF, DOC, DOCX up to 10MB):' : 'Upload official evaluation report (PDF, DOC, DOCX up to 10MB):' }}
                                </label>
                                <div class="sup-eval-input-group">
                                    <input type="file" id="modal-eval-file-{{ $application->id }}" name="evaluation" accept=".pdf,.doc,.docx" required>
                                    <button type="submit" class="btn-sup-upload-action">
                                        {{ $application->supervisor_evaluation_path ? 'Replace Report' : 'Submit Evaluation' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="sup-modal-footer">
                    <button type="button" class="btn-sup-modal-dismiss" data-modal-close="internDetailsModal-{{ $application->id }}">Close</button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Interactive Supervisor Scripts: Tab Switching, Live Search & Modal Management --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Tab Switching Controller
            const tabButtons = document.querySelectorAll('[data-tab-target]');
            const tabPanes = document.querySelectorAll('.supervisor-tab-pane');

            function activateTab(targetId) {
                if (!targetId) return;

                // Update active state on all tab buttons & sidebar links
                tabButtons.forEach(btn => {
                    const matches = btn.getAttribute('data-tab-target') === targetId;
                    btn.classList.toggle('active', matches);
                    if (btn.hasAttribute('aria-selected')) {
                        btn.setAttribute('aria-selected', matches ? 'true' : 'false');
                    }
                });

                // Update active state on panes
                tabPanes.forEach(pane => {
                    if (pane.id === targetId) {
                        pane.classList.add('active');
                        pane.style.display = 'block';
                    } else {
                        pane.classList.remove('active');
                        pane.style.display = 'none';
                    }
                });
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('data-tab-target');
                    if (targetId) {
                        activateTab(targetId);
                        history.replaceState(null, '', '#' + targetId);
                    }
                });
            });

            // Handle URL Hash or Query parameter on initial load
            const urlHash = window.location.hash ? window.location.hash.substring(1) : '';
            const urlParams = new URLSearchParams(window.location.search);
            if (urlHash === 'interns-section' || urlParams.get('tab') === 'interns') {
                activateTab('interns-section');
            } else if (urlHash === 'overview-section' || urlParams.get('tab') === 'overview') {
                activateTab('overview-section');
            }

            // 2. Intern Details Modal Controller
            const openModalButtons = document.querySelectorAll('[data-modal-target]');
            const closeModalElements = document.querySelectorAll('[data-modal-close]');

            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('active');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }
            }

            openModalButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetModalId = this.getAttribute('data-modal-target');
                    openModal(targetModalId);
                });
            });

            closeModalElements.forEach(elem => {
                elem.addEventListener('click', function (e) {
                    const targetModalId = this.getAttribute('data-modal-close');
                    closeModal(targetModalId);
                });
            });

            // Close active modal on Escape key
            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const activeModal = document.querySelector('.sup-details-modal-overlay.active');
                    if (activeModal) {
                        closeModal(activeModal.id);
                    }
                }
            });

            // 3. Client-side Realtime Search Filter
            const searchInput = document.getElementById('internSearchInput');
            const internRows = document.querySelectorAll('.intern-row-item');

            if (searchInput && internRows.length > 0) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    internRows.forEach(row => {
                        const rowText = row.getAttribute('data-search-text') || '';
                        row.style.display = rowText.includes(query) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>
