@extends('intern.layout')
@section('title', 'Intern Dashboard')

@section('content')
<div class="intern-dashboard-container">
    {{-- Top Navigation Section Tabs --}}
    <div class="intern-section-tabs-bar">
        <div class="intern-tabs-list" role="tablist">
            <button type="button" class="intern-tab-btn active" data-tab-target="all-sections" role="tab">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                    <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                    <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                    <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                </svg>
                <span>All Overview</span>
            </button>
            <button type="button" class="intern-tab-btn" data-tab-target="courses-section" role="tab">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <rect width="20" height="14" x="2" y="7" rx="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <span>Open Roles</span>
                <span class="intern-tab-count">{{ $courses->count() }}</span>
            </button>
            <button type="button" class="intern-tab-btn" data-tab-target="applications-section" role="tab">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="m9 14 2 2 4-4"></path>
                </svg>
                <span>My Applications</span>
                <span class="intern-tab-count">{{ $applications->count() }}</span>
            </button>
            <button type="button" class="intern-tab-btn" data-tab-target="institution-section" role="tab">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                    <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                </svg>
                <span>My Institution</span>
                @if ($currentInstitutionStatus === 'pending')
                    <span class="intern-tab-count badge-tab-pending">Pending</span>
                @elseif ($currentInstitutionStatus === 'registered')
                    <span class="intern-tab-count badge-tab-reg">✓</span>
                @endif
            </button>
        </div>
    </div>

    {{-- SECTION 1: Judiciary Departments & Open Roles --}}
    <section class="admin-panel intern-panel-section" id="courses-section">
        <div class="admin-panel-heading">
            <div>
                <h2>Judiciary Departments &amp; Open Roles</h2>
                <p>Select a Judiciary Directorate to view available internship placements and apply matching your field of study.</p>
            </div>
        </div>

        {{-- Official Judiciary Departments Grid --}}
        <div class="intern-departments-grid">
            @foreach ($departments as $dept)
                <div class="department-card {{ $dept->roles->count() > 0 ? 'has-roles' : 'no-roles' }}">
                    <div class="dept-card-header">
                        <div class="dept-icon-box">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M3 9h18"></path>
                                <path d="M9 21V9"></path>
                            </svg>
                        </div>
                        <div class="dept-header-tags">
                            @if ($dept->code)
                                <span class="dept-code-tag">{{ $dept->code }}</span>
                            @endif
                            @if ($dept->roles->count() > 0)
                                <span class="dept-active-badge">{{ $dept->roles->count() }} Open {{ $dept->roles->count() === 1 ? 'Role' : 'Roles' }}</span>
                            @else
                                <span class="dept-empty-badge">No Open Roles</span>
                            @endif
                        </div>
                    </div>

                    <h3 class="dept-title">{{ $dept->name }}</h3>
                    @if ($dept->description)
                        <p class="dept-desc">{{ $dept->description }}</p>
                    @endif

                    @if ($dept->roles->count() > 0)
                        <div class="dept-roles-list">
                            <span class="dept-roles-subheading">Available Roles in Directorate:</span>
                            @foreach ($dept->roles as $deptRole)
                                <div class="dept-role-row">
                                    <div class="dept-role-info">
                                        <span class="dept-role-name">{{ $deptRole->name }}</span>
                                        @if ($deptRole->field_of_study)
                                            <span class="dept-role-field">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                                </svg>
                                                {{ $deptRole->field_of_study }}
                                            </span>
                                        @endif
                                    </div>
                                    @if (in_array($deptRole->id, $appliedCourseIds, true))
                                        <span class="admin-role app-submitted-pill" style="font-size: 0.76rem; padding: 0.25rem 0.6rem;">Applied</span>
                                    @else
                                        <a href="{{ route('intern.courses.apply', $deptRole) }}" class="btn-dept-apply-role" title="Apply for {{ $deptRole->name }}">
                                            Apply &rarr;
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="dept-empty-notice">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>No open roles posted for this department yet.</span>
                        </div>
                    @endif

                    <div class="dept-card-footer">
                        @if ($dept->roles->count() > 0)
                            <a href="{{ route('intern.departments.apply', $dept) }}" class="btn-dept-card-apply">
                                <span>Apply in this Department</span>
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        @else
                            <button type="button" class="btn-dept-card-disabled" disabled>
                                No Open Roles
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- SECTION 2: My Applications --}}
    <section class="admin-panel intern-panel-section" id="applications-section">
        <div class="admin-panel-heading">
            <div>
                <h2>My Applications</h2>
                <p>Track the progress of your submitted applications and download recommendation letters.</p>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Course / Position</th>
                        <th>Institution</th>
                        <th>Submitted Documents</th>
                        <th>Application Status</th>
                        <th>Recommendation Letter</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        <tr>
                            <td data-label="Course / Position">
                                <strong>{{ $application->course?->name ?? $application->internship?->course?->name ?? '—' }}</strong>
                            </td>
                            <td data-label="Institution">
                                @php
                                    $appliedUni = $application->institution ?? auth()->user()->school;
                                    $uniStatus = $appliedUni ? ($universityStatusMap[$appliedUni] ?? null) : null;
                                @endphp
                                <div class="intern-institution-cell">
                                    <span class="intern-institution-name">{{ $appliedUni ?? '—' }}</span>
                                    @if ($uniStatus === 'pending')
                                        <span class="uni-status-pill status-pill-pending" title="Your university has been submitted and is pending HR approval">
                                            Pending HR Approval
                                        </span>
                                    @elseif ($uniStatus === 'registered')
                                        <span class="uni-status-pill status-pill-registered" title="Accredited & registered institution">
                                            Registered
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Submitted Documents">
                                <div class="intern-docs-pills">
                                    @if ($application->school_recommendation_path)
                                        <a href="{{ route('intern.applications.document', [$application, 'type' => 'school_recommendation', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open School Recommendation Letter">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                            </svg>
                                            <span>School Rec</span>
                                        </a>
                                    @endif

                                    @if ($application->insurance_path)
                                        <a href="{{ route('intern.applications.document', [$application, 'type' => 'insurance', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Insurance Cover">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                            </svg>
                                            <span>Insurance</span>
                                        </a>
                                    @endif

                                    @if ($application->cv_path)
                                        <a href="{{ route('intern.applications.document', [$application, 'type' => 'cv', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Application Letter / CV">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                            </svg>
                                            <span>CV / Letter</span>
                                        </a>
                                    @endif

                                    @if (!$application->school_recommendation_path && !$application->insurance_path && !$application->cv_path)
                                        @if ($application->document_path)
                                            <a href="{{ route('intern.applications.document', [$application, 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Attached Document">
                                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                </svg>
                                                <span>Document</span>
                                            </a>
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem;">None uploaded</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td data-label="Application Status">
                                <span class="hr-status hr-status-{{ $application->status }}">
                                    {{ str($application->status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td data-label="Recommendation Letter">
                                @if ($application->recommendation_letter_path)
                                    <a class="admin-primary-link" href="{{ route('intern.recommendation.download', $application) }}">
                                        Download Letter
                                    </a>
                                @else
                                    <span class="text-muted">Not issued yet</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="admin-empty" colspan="5">
                                You have not submitted any course applications yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- SECTION 3: My Academic Institution & Universities --}}
    <section class="admin-panel intern-panel-section" id="institution-section">
        <div class="admin-panel-heading">
            <div>
                <h2>My Academic Institution & Universities</h2>
                <p>Select your university or request approval for an unlisted institution.</p>
            </div>
        </div>

        <div class="intern-institution-workspace">
            {{-- Current Institution Status Banner --}}
            <div class="intern-uni-status-card {{ $currentInstitutionStatus === 'pending' ? 'card-pending' : ($currentInstitutionStatus === 'registered' ? 'card-registered' : '') }}">
                <div class="uni-status-card-icon">
                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none">
                        <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                        <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                    </svg>
                </div>
                <div class="uni-status-card-content">
                    <span class="status-card-label">Your Current Registered Institution</span>
                    @if ($currentInstitution)
                        <h3 class="status-card-name">{{ $currentInstitution }}</h3>
                        <div class="status-card-badges">
                            @if ($currentInstitutionStatus === 'pending')
                                <span class="uni-status-badge status-pending">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Pending HR Approval
                                </span>
                                <span class="status-card-hint">
                                    Judiciary HR has been notified to review and approve this institution for accreditation.
                                </span>
                            @elseif ($currentInstitutionStatus === 'registered')
                                <span class="uni-status-badge status-registered">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Accredited & Registered
                                </span>
                                <span class="status-card-hint">
                                    Your institution is fully accredited and verified on the Judiciary portal.
                                </span>
                            @else
                                <span class="uni-status-badge status-registered">Saved on Profile</span>
                            @endif
                        </div>
                    @else
                        <h3 class="status-card-name status-not-set">No Institution Selected Yet</h3>
                        <p class="status-card-hint">
                            Select your university from our accredited directory or submit a request for an unlisted college.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Select or Request Institution Form --}}
            <div class="intern-uni-form-card">
                <h3>Select or Request Institution</h3>
                <p>Choose an accredited university or enter your institution details if not yet registered.</p>

                <form method="POST" action="{{ route('intern.institution.update') }}" class="admin-form intern-uni-form" id="dashUniForm">
                    @csrf

                    <div class="admin-form-field">
                        <label for="dash_institution_select">Select University / Institution <span class="required">*</span></label>
                        <select id="dash_institution_select" name="institution_select" class="admin-select searchable-university-select" data-searchable-university="true" required>
                            <option value="" disabled {{ $currentInstitution ? '' : 'selected' }}>
                                -- Select your university --
                            </option>
                            <optgroup label="Accredited & Registered Universities">
                                @foreach ($registeredUniversities as $uni)
                                    <option value="{{ $uni->name }}" data-location="{{ $uni->location ?? '' }}" {{ $currentInstitution === $uni->name ? 'selected' : '' }}>
                                        {{ $uni->name }} @if($uni->location) ({{ $uni->location }}) @endif
                                    </option>
                                @endforeach
                            </optgroup>
                            <option value="__other__" data-is-other="true">
                                ➕ Institution not in list? Add yours for HR approval...
                            </option>
                        </select>
                    </div>

                    {{-- Collapsible Custom Fields --}}
                    <div id="dashCustomSection" class="custom-uni-box" style="display: none;">
                        <div class="custom-uni-header">
                            <div class="custom-uni-icon">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                                    <path d="M12 2l10 6-10 6-10-6 10-6z"></path>
                                    <path d="M4 14l8 5 8-5"></path>
                                </svg>
                            </div>
                            <div>
                                <h4>Add Institution</h4>
                            </div>
                        </div>

                        <div class="admin-form-field">
                            <label for="dash_custom_name">Institution Name <span class="required">*</span></label>
                            <input
                                type="text"
                                id="dash_custom_name"
                                name="custom_institution_name"
                                placeholder="e.g. Daystar University / Multimedia University"
                                maxlength="255"
                            >
                        </div>

                        <div class="admin-form-field">
                            <label for="dash_custom_location">Campus / Town / City</label>
                            <input
                                type="text"
                                id="dash_custom_location"
                                name="custom_institution_location"
                                placeholder="e.g. Nairobi Campus / Main Campus, Juja"
                                maxlength="255"
                            >
                        </div>

                        <div class="custom-uni-alert">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>
                                <strong>HR Workflow:</strong> Submitting this institution creates a pending request. HR will review and approve it. It will also be linked to all your applications.
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="admin-submit btn-save-uni">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Update Institution</span>
                    </button>
                </form>
            </div>

        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Interactive Tabs System ---
        const tabBtns = document.querySelectorAll('.intern-tab-btn');
        const sections = document.querySelectorAll('.intern-panel-section');
        const sidebarLinks = document.querySelectorAll('#internSidebarNav .sidebar-link');

        function activateTab(target) {
            // Update active states on tab buttons
            tabBtns.forEach(btn => {
                const isTarget = btn.getAttribute('data-tab-target') === target;
                btn.classList.toggle('active', isTarget);
            });

            // Update active state on sidebar links
            sidebarLinks.forEach(link => {
                const linkTarget = link.getAttribute('data-tab');
                if (target === 'all-sections') {
                    link.classList.toggle('active', linkTarget === 'all-sections');
                } else {
                    link.classList.toggle('active', linkTarget === target);
                }
            });

            // Show or filter sections
            if (target === 'all-sections') {
                sections.forEach(s => s.style.display = 'block');
            } else {
                sections.forEach(s => {
                    if (s.id === target) {
                        s.style.display = 'block';
                        s.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        s.style.display = 'none';
                    }
                });
            }
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-tab-target');
                activateTab(target);
                if (target !== 'all-sections') {
                    history.replaceState(null, null, '#' + target);
                } else {
                    history.replaceState(null, null, window.location.pathname);
                }
            });
        });

        // Sidebar link integration
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const target = this.getAttribute('data-tab');
                if (target && document.getElementById(target)) {
                    e.preventDefault();
                    activateTab(target);
                    history.replaceState(null, null, '#' + target);
                } else if (target === 'all-sections') {
                    e.preventDefault();
                    activateTab('all-sections');
                    history.replaceState(null, null, window.location.pathname);
                }
            });
        });

        // Handle initial URL hash or query params (?tab=courses, etc.)
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        const hash = window.location.hash.replace('#', '');

        if (tabParam === 'institution' || hash === 'institution-section') {
            activateTab('institution-section');
        } else if (tabParam === 'courses' || hash === 'courses-section') {
            activateTab('courses-section');
        } else if (tabParam === 'applications' || hash === 'applications-section') {
            activateTab('applications-section');
        }

        // --- 2. Custom Institution Collapsible for Dashboard Form ---
        const dashSelect = document.getElementById('dash_institution_select');
        const dashCustom = document.getElementById('dashCustomSection');
        const dashCustomName = document.getElementById('dash_custom_name');

        function toggleDashCustom() {
            if (!dashSelect || !dashCustom) return;
            if (dashSelect.value === '__other__') {
                dashCustom.style.display = 'flex';
                dashCustom.classList.add('active');
                if (dashCustomName) {
                    dashCustomName.setAttribute('required', 'required');
                    dashCustomName.focus();
                }
            } else {
                dashCustom.style.display = 'none';
                dashCustom.classList.remove('active');
                if (dashCustomName) {
                    dashCustomName.removeAttribute('required');
                }
            }
        }

        if (dashSelect) {
            dashSelect.addEventListener('change', toggleDashCustom);
            toggleDashCustom();
        }

    });
</script>
@endpush
