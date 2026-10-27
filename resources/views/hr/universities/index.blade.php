@extends('hr.layout')
@section('title', 'Universities & Institutions')
@section('heading', 'Universities & Institutions')

@section('content')
<div class="hr-universities-container">
    {{-- Top Metrics Summary Cards --}}
    <div class="hr-metrics-grid">
        <div class="hr-metric-card">
            <div class="hr-metric-icon icon-all">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                    <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                </svg>
            </div>
            <div class="hr-metric-data">
                <span class="hr-metric-title">Total Institutions</span>
                <span class="hr-metric-value">{{ $totalCount }}</span>
            </div>
        </div>

        <a href="{{ route('hr.universities.index', ['status' => 'registered']) }}" class="hr-metric-card {{ $status === 'registered' ? 'metric-active' : '' }}">
            <div class="hr-metric-icon icon-registered">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="hr-metric-data">
                <span class="hr-metric-title">Registered / Approved</span>
                <span class="hr-metric-value text-registered">{{ $registeredCount }}</span>
            </div>
        </a>

        <a href="{{ route('hr.universities.index', ['status' => 'pending']) }}" class="hr-metric-card {{ $status === 'pending' ? 'metric-active' : '' }} {{ $pendingCount > 0 ? 'metric-attention' : '' }}">
            <div class="hr-metric-icon icon-pending">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="hr-metric-data">
                <span class="hr-metric-title">Pending Approval</span>
                <span class="hr-metric-value text-pending">{{ $pendingCount }}</span>
            </div>
        </a>
    </div>

    {{-- Pending Approval Alert Banner --}}
    @if ($pendingCount > 0 && $status !== 'pending')
        <div class="hr-pending-alert">
            <div class="alert-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="alert-content">
                <strong>Attention: Action Required</strong>
                <p>There are <strong>{{ $pendingCount }}</strong> university approval request(s) submitted by interns waiting for your confirmation.</p>
            </div>
            <a href="{{ route('hr.universities.index', ['status' => 'pending']) }}" class="btn-review-pending">
                Review Requests ({{ $pendingCount }})
            </a>
        </div>
    @endif

    {{-- Main Workspace: Form & Table Grid --}}
    <div class="hr-uni-workspace">
        {{-- Add University Card Form --}}
        <div class="hr-uni-sidebar">
            <div class="admin-panel hr-uni-form-card">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Add University</h2>
                        <p>Register a new accredited institution</p>
                    </div>
                </div>

                <form class="admin-form uni-add-form" method="POST" action="{{ route('hr.universities.store') }}">
                    @csrf
                    <div class="admin-form-field">
                        <label for="name">University Name <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Daystar University"
                            required
                            maxlength="255"
                        >
                    </div>

                    <div class="admin-form-field">
                        <label for="location">Campus / Location</label>
                        <input
                            type="text"
                            id="location"
                            name="location"
                            value="{{ old('location') }}"
                            placeholder="e.g. Nairobi, Kenya"
                            maxlength="255"
                        >
                    </div>

                    <div class="uni-form-help">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Universities added by HR are automatically registered and available for intern selection immediately.</span>
                    </div>

                    <button class="admin-submit uni-submit-btn" type="submit">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <span>Add & Register University</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Universities Table Section --}}
        <div class="hr-uni-main">
            <section class="admin-panel">
                <div class="admin-panel-heading hr-table-top-bar">
                    <div class="hr-table-tabs">
                        <a href="{{ route('hr.universities.index', ['status' => 'all', 'search' => $search]) }}"
                           class="role-filter-tab {{ $status === 'all' ? 'active' : '' }}">
                            All Institutions
                            <span class="role-pill-count">{{ $totalCount }}</span>
                        </a>
                        <a href="{{ route('hr.universities.index', ['status' => 'registered', 'search' => $search]) }}"
                           class="role-filter-tab {{ $status === 'registered' ? 'active' : '' }}">
                            Registered
                            <span class="role-pill-count status-count-reg">{{ $registeredCount }}</span>
                        </a>
                        <a href="{{ route('hr.universities.index', ['status' => 'pending', 'search' => $search]) }}"
                           class="role-filter-tab {{ $status === 'pending' ? 'active' : '' }} {{ $pendingCount > 0 ? 'tab-has-pending' : '' }}">
                            Pending Approval
                            <span class="role-pill-count status-count-pend">{{ $pendingCount }}</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('hr.universities.index') }}" class="hr-search-form">
                        @if ($status !== 'all')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <div class="search-input-wrapper">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search institutions..."
                                class="hr-table-search-input"
                            >
                            @if ($search !== '')
                                <a href="{{ route('hr.universities.index', ['status' => $status]) }}" class="clear-search-btn" title="Clear search">✕</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table hr-uni-table">
                        <thead>
                            <tr>
                                <th>Institution Name</th>
                                <th>Location / Campus</th>
                                <th>Status</th>
                                <th>Origin / Requested By</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($universities as $university)
                                <tr class="{{ $university->isPending() ? 'row-pending-review' : '' }}">
                                    <td data-label="Institution Name">
                                        <div class="uni-name-cell">
                                            <div class="uni-icon-circle">
                                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none">
                                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="uni-name-text">{{ $university->name }}</span>
                                                @if ($university->courses_count > 0)
                                                    <span class="uni-course-tag">{{ $university->courses_count }} position(s)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Location / Campus">
                                        <div class="uni-location-cell">
                                            @if ($university->location)
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <span>{{ $university->location }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Status">
                                        @if ($university->isRegistered())
                                            <span class="uni-status-badge status-registered" title="Approved and available to interns">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Registered
                                            </span>
                                        @else
                                            <span class="uni-status-badge status-pending" title="Awaiting HR approval">
                                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                Pending Approval
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Origin / Requested By">
                                        @if ($university->requester)
                                            <div class="requester-info" title="Requested by intern: {{ $university->requester->email }}">
                                                <span class="requester-name">{{ $university->requester->name }}</span>
                                                <span class="requester-role">Intern Request &bull; {{ $university->created_at?->diffForHumans() }}</span>
                                            </div>
                                        @else
                                            <div class="requester-info">
                                                <span class="requester-admin">Judiciary HR</span>
                                                <span class="requester-date">{{ $university->created_at?->format('M d, Y') ?? 'System' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td data-label="Actions" style="text-align: right;">
                                        <div class="admin-actions uni-actions-row">
                                            @if ($university->isPending())
                                                <form method="POST" action="{{ route('hr.universities.approve', $university) }}" style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-action-approve" title="Approve university and make it registered">
                                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            <a class="admin-edit-link" href="{{ route('hr.universities.edit', $university) }}" title="Edit details">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('hr.universities.destroy', $university) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($university->name) }}\'?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="admin-delete-button" type="submit" title="Delete university">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="admin-empty" colspan="5">
                                        <div class="empty-state-box">
                                            <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="1.5" fill="none">
                                                <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                                                <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                                            </svg>
                                            @if ($status === 'pending')
                                                <p>No universities are currently pending approval. All submitted institutions have been processed!</p>
                                            @elseif ($search !== '')
                                                <p>No universities matched your search "<strong>{{ $search }}</strong>".</p>
                                                <a href="{{ route('hr.universities.index', ['status' => $status]) }}" class="admin-secondary-link">Clear search</a>
                                            @else
                                                <p>No universities found in this category.</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($universities->hasPages())
                    <div class="admin-pagination">{{ $universities->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection