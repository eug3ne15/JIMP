@extends('hr.layout')

@section('title', $course->name . ' - Position Details & Statistics')
@section('heading', 'Position Details & Management')

@section('content')
<div class="hr-role-detail-container">
    {{-- Header Banner Card --}}
    <div class="hr-role-detail-header-card">
        <div class="hr-role-header-content">
            <a class="admin-btn-back" href="{{ route('hr.courses.index') }}" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border-color: rgba(255, 255, 255, 0.3); margin-bottom: 0.75rem;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Open Positions</span>
            </a>
            <h1>{{ $course->name }}</h1>
            <div class="hr-role-header-badges">
                @if ($course->department)
                    <span class="hr-header-dept-tag">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                        {{ $course->department->name }} @if($course->department->code) ({{ $course->department->code }}) @endif
                    </span>
                @else
                    <span class="hr-header-dept-tag">Judiciary Open Directorate</span>
                @endif

                @if ($course->isOpen())
                    <span class="hr-intake-badge is-open" style="font-size: 0.8rem; padding: 0.25rem 0.65rem;">Open for Intake</span>
                @else
                    <span class="hr-intake-badge is-closed" style="font-size: 0.8rem; padding: 0.25rem 0.65rem;">Closed</span>
                @endif
            </div>
        </div>

        <div>
            <button
                type="button"
                class="admin-primary-link js-edit-course-btn"
                data-id="{{ $course->id }}"
                data-name="{{ $course->name }}"
                data-department-id="{{ $course->department_id ?? '' }}"
                data-field-of-study="{{ $course->field_of_study ?? '' }}"
                data-status="{{ $course->status ?? 'open' }}"
                data-description="{{ $course->description ?? '' }}"
                data-requirements="{{ $course->requirements ?? '' }}"
                data-university-id="{{ $course->university_id ?? '' }}"
                data-action="{{ route('hr.courses.update', $course) }}"
                style="background: var(--judiciary-gold, #d5a62e); color: var(--judiciary-deep-green, #025532); font-weight: 700; border: none; cursor: pointer; padding: 0.6rem 1.15rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"
            >
                Edit Position Details
            </button>
        </div>
    </div>

    {{-- Statistics Overview Metrics Grid --}}
    <div class="hr-stats-overview-grid">
        {{-- Stat 1: Fields of Study Count --}}
        <div class="hr-stat-metric-card">
            <div class="metric-icon-wrap metric-fields-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <div class="metric-body">
                <span class="metric-number">{{ $course->fields_of_study_count }}</span>
                <span class="metric-label">Fields of Study</span>
                <span class="metric-subtext">{{ $course->fields_of_study_count > 0 ? 'Dropdown options active' : 'Awaiting addition' }}</span>
            </div>
        </div>

        {{-- Stat 2: Total Applicants --}}
        <div class="hr-stat-metric-card">
            <div class="metric-icon-wrap metric-apps-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="metric-body">
                <span class="metric-number">{{ $course->applications_count }}</span>
                <span class="metric-label">Total Applicants</span>
                <span class="metric-subtext">{{ $pendingApplicantsCount }} pending HR vetting</span>
            </div>
        </div>

        {{-- Stat 3: Active Placements --}}
        <div class="hr-stat-metric-card">
            <div class="metric-icon-wrap metric-placed-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                    <rect width="20" height="14" x="2" y="7" rx="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
            <div class="metric-body">
                <span class="metric-number">{{ $course->internships_count }}</span>
                <span class="metric-label">Active Placements</span>
                <span class="metric-subtext">{{ $shortlistedCount }} candidate(s) shortlisted</span>
            </div>
        </div>

        {{-- Stat 4: Directorate Linkage --}}
        <div class="hr-stat-metric-card">
            <div class="metric-icon-wrap metric-dept-icon">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                    <path d="M3 9h18"></path>
                    <path d="M9 21V9"></path>
                </svg>
            </div>
            <div class="metric-body">
                <span class="metric-number" style="font-size: 1.15rem; font-weight: 700;">{{ $course->department?->code ?? 'JUD' }}</span>
                <span class="metric-label">{{ $course->department?->name ?? 'Judiciary Directorate' }}</span>
                <span class="metric-subtext">Judiciary Station</span>
            </div>
        </div>
    </div>

    {{-- Fields of Study Management Section --}}
    <section class="fields-management-card">
        <div class="fields-management-header">
            <div>
                <h2>Eligible Fields of Study ({{ $course->fields_of_study_count }})</h2>
                <p>These disciplines are automatically displayed as dropdown options when interns apply for this position.</p>
            </div>
            @if ($course->fields_of_study_count > 0)
                <span class="hr-stat-pill stat-fields" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                    ✓ {{ $course->fields_of_study_count }} Active Discipline{{ $course->fields_of_study_count === 1 ? '' : 's' }}
                </span>
            @else
                <span class="hr-stat-pill stat-fields-none" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                    ⚠️ No Fields Configured
                </span>
            @endif
        </div>

        {{-- Add Field of Study Form Box --}}
        <div class="add-field-form-box">
            <div class="add-field-box-header">
                <div class="add-field-icon-wrap">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h4>Add New Field(s) of Study</h4>
                    <p>Enter an academic discipline or type multiple disciplines separated by commas (e.g. <code>Computer Science, Software Engineering, IT</code>). Once added, candidates can select them directly in the application form.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('hr.courses.fields.add', $course) }}" class="add-field-modern-form">
                @csrf
                <div class="add-field-input-group">
                    <div class="input-with-icon-wrap">
                        <svg class="input-field-icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        <input
                            type="text"
                            id="field_of_study_input"
                            name="field_of_study"
                            placeholder="e.g. Computer Science, Software Engineering, Information Technology"
                            required
                            maxlength="255"
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="btn-add-field-submit">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add Field of Study</span>
                    </button>
                </div>
                @error('field_of_study')
                    <span class="field-error" style="display: block; margin-top: 0.4rem; color: #dc2626; font-size: 0.82rem;">{{ $message }}</span>
                @enderror
                </div>
            </form>
        </div>

        {{-- Configured Disciplines Showcase --}}
        <div class="disciplines-section-heading">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Configured Disciplines ({{ $course->fields_of_study_count }})</h3>
                <span style="font-size: 0.8rem; color: #64748b;">Active disciplines currently available in the intern application dropdown</span>
            </div>
            @if ($course->fields_of_study_count > 0)
                <span style="font-size: 0.78rem; font-weight: 700; color: #025532; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                    ● Live in Dropdown
                </span>
            @endif
        </div>

        <div class="disciplines-cards-grid">
            @forelse ($fieldsOfStudy as $field)
                @php
                    $fStats = $fieldsCountMap->get(strtolower(trim($field)));
                    $fAppCount = $fStats['total_applications'] ?? 0;
                    $fPending = $fStats['pending'] ?? 0;
                    $fShortlisted = $fStats['shortlisted'] ?? 0;
                    $fPlaced = $fStats['placed'] ?? 0;
                @endphp
                <article class="discipline-card">
                    <div class="discipline-card-header">
                        <div class="discipline-icon-badge">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div class="discipline-title-wrap">
                            <h4 class="discipline-title" title="{{ $field }}">{{ $field }}</h4>
                            <span class="discipline-live-tag">
                                <span class="live-dot"></span>
                                Active in Dropdown
                            </span>
                        </div>
                    </div>

                    <div class="discipline-card-body">
                        <div class="discipline-stat-banner {{ $fAppCount > 0 ? 'has-apps' : 'no-apps' }}">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span class="stat-count-text">{{ $fAppCount }} {{ $fAppCount === 1 ? 'applicant' : 'applicants' }}</span>
                            <span class="stat-count-suffix">registered</span>
                        </div>

                        @if ($fAppCount > 0)
                            <div class="discipline-stages-strip">
                                <span class="d-stage stage-pending" title="{{ $fPending }} Pending HR Review">
                                    <span class="dot dot-amber"></span> {{ $fPending }} Pending
                                </span>
                                <span class="d-stage stage-shortlisted" title="{{ $fShortlisted }} Shortlisted">
                                    <span class="dot dot-green"></span> {{ $fShortlisted }} Shortlisted
                                </span>
                                <span class="d-stage stage-placed" title="{{ $fPlaced }} Placed with Supervisor">
                                    <span class="dot dot-blue"></span> {{ $fPlaced }} Placed
                                </span>
                            </div>
                        @else
                            <p class="discipline-empty-subtext">No candidates registered for this field yet. Ready in dropdown.</p>
                        @endif
                    </div>

                    <div class="discipline-card-footer">
                        <span class="discipline-footer-label">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Eligible Discipline
                        </span>

                        <form method="POST" action="{{ route('hr.courses.fields.remove', $course) }}" class="discipline-delete-form inline-action-form" style="display: inline; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none; width: auto;" onsubmit="return confirm('Remove \'{{ addslashes($field) }}\' from this position? Interns will no longer be able to select it.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="field" value="{{ $field }}">
                            <button type="submit" class="btn-card-action btn-card-delete" title="Remove {{ $field }}" aria-label="Remove {{ $field }}">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <span>Remove</span>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="discipline-empty-box" style="grid-column: 1 / -1;">
                    <div class="discipline-empty-icon">
                        <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.6" fill="none">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <h4>No fields of study have been added yet for this role.</h4>
                    <p>Use the form above to add one or more academic disciplines. They will immediately become selectable in the intern application dropdown.</p>
                </div>
            @endforelse
        </div>

        {{-- Applications Breakdown by Field of Study Table --}}
        @if ($fieldsBreakdown->isNotEmpty() && $course->applications_count > 0)
            <div class="fields-breakdown-card" style="margin-top: 1.5rem; margin-bottom: 0.5rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0;">
                            Applications Breakdown by Field of Study
                        </h4>
                        <span style="font-size: 0.8rem; color: #64748b;">
                            Distribution of candidate applications across eligible academic disciplines
                        </span>
                    </div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: #025532; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                        {{ $course->applications_count }} Total {{ $course->applications_count === 1 ? 'Application' : 'Applications' }}
                    </span>
                </div>

                <div class="admin-table-wrap" style="box-shadow: none; border: 1px solid #f1f5f9; border-radius: 8px; overflow: hidden;">
                    <table class="admin-table" style="font-size: 0.85rem; margin: 0;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th>Field of Study</th>
                                <th style="text-align: center;">Total Applications</th>
                                <th style="text-align: center;">Pending Vetting</th>
                                <th style="text-align: center;">Shortlisted</th>
                                <th style="text-align: center;">Placed with Supervisor</th>
                                <th>Share of Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fieldsBreakdown as $b)
                                <tr>
                                    <td data-label="Field of Study" style="font-weight: 600; color: #1e293b;">
                                        🎓 {{ $b['name'] }}
                                    </td>
                                    <td data-label="Total Applications" style="text-align: center;">
                                        <span class="intern-tab-count" style="font-size: 0.82rem; font-weight: 700; background: {{ $b['total_applications'] > 0 ? '#dcfce7' : '#f1f5f9' }}; color: {{ $b['total_applications'] > 0 ? '#15803d' : '#64748b' }}; padding: 0.2rem 0.65rem; border-radius: 999px;">
                                             {{ $b['total_applications'] }}
                                        </span>
                                    </td>
                                    <td data-label="Pending Vetting" style="text-align: center;">
                                        <span style="color: {{ $b['pending'] > 0 ? '#b45309' : '#94a3b8' }}; font-weight: 600;">
                                            {{ $b['pending'] }}
                                        </span>
                                    </td>
                                    <td data-label="Shortlisted" style="text-align: center;">
                                        <span style="color: {{ $b['shortlisted'] > 0 ? '#15803d' : '#94a3b8' }}; font-weight: 600;">
                                            {{ $b['shortlisted'] }}
                                        </span>
                                    </td>
                                    <td data-label="Placed with Supervisor" style="text-align: center;">
                                        <span style="color: {{ $b['placed'] > 0 ? '#1d4ed8' : '#94a3b8' }}; font-weight: 600;">
                                            {{ $b['placed'] }}
                                        </span>
                                    </td>
                                    <td data-label="Share of Position">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden; min-width: 60px; max-width: 100px;">
                                                <div style="width: {{ $b['percentage'] }}%; height: 100%; background: var(--judiciary-deep-green, #025532); border-radius: 999px;"></div>
                                            </div>
                                            <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">{{ $b['percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>

    {{-- Position Scope & Academic Requirements --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
        <div class="admin-panel" style="margin: 0;">
            <div class="admin-panel-heading" style="padding-bottom: 0.75rem; border-bottom: 1px solid #edf2f7; margin-bottom: 0.85rem;">
                <h3>Role Scope &amp; Responsibilities</h3>
            </div>
            <p style="color: #334155; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                {{ $course->description ?: 'No detailed scope description provided yet. Click "Edit Position Details" above to add responsibilities.' }}
            </p>
        </div>

        <div class="admin-panel" style="margin: 0;">
            <div class="admin-panel-heading" style="padding-bottom: 0.75rem; border-bottom: 1px solid #edf2f7; margin-bottom: 0.85rem;">
                <h3>Academic &amp; Eligibility Requirements</h3>
            </div>
            <p style="color: #334155; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                {{ $course->requirements ?: 'Enrolled in an accredited higher learning institution with a recommendation letter from Dean/Coordinator.' }}
            </p>
            @if ($course->university)
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #edf2f7; font-size: 0.82rem; color: #64748b;">
                    <strong>Restricted to:</strong> {{ $course->university->name }}
                </div>
            @endif
        </div>
    </div>

    {{-- Applicants for this Position --}}
    <section class="admin-panel" style="margin: 0;">
        <div class="admin-panel-heading">
            <div>
                <h2>Applicants for this Position ({{ $course->applications_count }})</h2>
                <p>Candidates who have applied specifically for the {{ $course->name }} opening.</p>
            </div>
            @if ($course->applications_count > 0)
                <a href="{{ route('hr.intern.index', ['course_id' => $course->id]) }}" class="admin-primary-link" style="font-size: 0.82rem; padding: 0.4rem 0.85rem;">
                    Review in Interns Manager &rarr;
                </a>
            @endif
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Institution</th>
                        <th>Selected Field of Study</th>
                        <th>Status</th>
                        <th>Assigned Supervisor</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($course->applications as $application)
                        <tr>
                            <td data-label="Candidate Name">
                                <strong>{{ $application->applicant_name ?: $application->user->name }}</strong>
                                <span class="hr-table-subtitle">{{ $application->user->email }}</span>
                            </td>
                            <td data-label="Institution">
                                {{ $application->institution ?: $application->user->school ?: '—' }}
                            </td>
                            <td data-label="Field of Study">
                                <span class="badge-field-study" style="font-size: 0.8rem; padding: 0.15rem 0.55rem; background: #f0fdf4; color: #166534; border-radius: 999px; border: 1px solid #bbf7d0;">
                                    {{ $application->field_of_study ?: 'General' }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="hr-status hr-status-{{ $application->status }}">
                                    {{ str($application->status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td data-label="Assigned Supervisor">
                                @if ($application->internship?->supervisor)
                                    <span class="intern-tab-count" style="background: #eff6ff; color: #1d4ed8; font-weight: 600; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        👤 {{ $application->internship->supervisor->name }}
                                    </span>
                                @else
                                    <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Unassigned</span>
                                @endif
                            </td>
                            <td data-label="Actions" style="text-align: right;">
                                <a href="{{ route('hr.intern.show', $application) }}" class="admin-edit-link" style="font-size: 0.8rem;">
                                    Vet Candidate
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="admin-empty" colspan="6">No candidates have applied for this position yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- Edit Open Role Modal --}}
<div id="editCourseModal" class="admin-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalCourseTitle">
    <div class="admin-modal-dialog" style="max-width: 620px;">
        <div class="admin-modal-header">
            <div>
                <h3 id="modalCourseTitle">Edit Open Position</h3>
                <p id="modalCourseSubtitle" class="admin-field-hint">Update position designation, department, and intake status</p>
            </div>
            <button type="button" class="admin-modal-close" id="closeCourseModalBtn" aria-label="Close dialog">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form id="editCourseForm" class="admin-modal-form" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="admin-form-field">
                <label for="modal_course_name">Position Title / Designation <span style="color: #b91c1c;">*</span></label>
                <input id="modal_course_name" name="name" required maxlength="255">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="admin-form-field">
                    <label for="modal_course_department">Judiciary Department</label>
                    <select id="modal_course_department" name="department_id">
                        <option value="">-- General / Open Directorate --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-form-field">
                    <label for="modal_course_status">Intake Status</label>
                    <select id="modal_course_status" name="status">
                        <option value="open">Open for Intake</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="admin-form-field">
                <label for="modal_course_field">Eligible Field(s) of Study <span class="admin-field-hint">(comma-separated)</span></label>
                <input id="modal_course_field" name="field_of_study" maxlength="255" placeholder="e.g. Computer Science, Software Engineering, IT">
            </div>

            <div class="admin-form-field">
                <label for="modal_course_description">Role Scope &amp; Responsibilities</label>
                <textarea id="modal_course_description" name="description" rows="3"></textarea>
            </div>

            <div class="admin-form-field">
                <label for="modal_course_requirements">Academic &amp; Eligibility Requirements</label>
                <textarea id="modal_course_requirements" name="requirements" rows="2"></textarea>
            </div>

            <div class="admin-form-field">
                <label for="modal_course_university">Institution Linkage <span class="admin-field-hint">(optional restriction)</span></label>
                <select id="modal_course_university" name="university_id">
                    <option value="">-- General / Open to All Institutions --</option>
                    @foreach ($universities as $uni)
                        <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-modal-actions">
                <button type="button" class="admin-btn-secondary" id="cancelCourseModalBtn">Cancel</button>
                <button type="submit" class="admin-submit" style="width: auto; min-height: 2.4rem; padding: 0.6rem 1.25rem;">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
window.addFieldSuggestion = function (field) {
    const input = document.getElementById('field_of_study_input');
    if (!input) return;
    const current = input.value.trim();
    if (!current) {
        input.value = field;
    } else {
        const parts = current.split(',').map(s => s.trim()).filter(Boolean);
        if (!parts.some(p => p.toLowerCase() === field.toLowerCase())) {
            parts.push(field);
            input.value = parts.join(', ');
        }
    }
    input.focus();
};

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editCourseModal');
    const form = document.getElementById('editCourseForm');
    const closeBtn = document.getElementById('closeCourseModalBtn');
    const cancelBtn = document.getElementById('cancelCourseModalBtn');
    const modalSubtitle = document.getElementById('modalCourseSubtitle');
    const nameInput = document.getElementById('modal_course_name');
    const deptSelect = document.getElementById('modal_course_department');
    const fieldInput = document.getElementById('modal_course_field');
    const statusSelect = document.getElementById('modal_course_status');
    const descTextarea = document.getElementById('modal_course_description');
    const reqTextarea = document.getElementById('modal_course_requirements');
    const universitySelect = document.getElementById('modal_course_university');

    function openModal(data) {
        form.action = data.action;
        nameInput.value = data.name || '';
        if (deptSelect) deptSelect.value = data.departmentId || '';
        if (fieldInput) fieldInput.value = data.fieldOfStudy || '';
        if (statusSelect) statusSelect.value = data.status || 'open';
        if (descTextarea) descTextarea.value = data.description || '';
        if (reqTextarea) reqTextarea.value = data.requirements || '';
        if (universitySelect) universitySelect.value = data.universityId || '';

        if (modalSubtitle) {
            modalSubtitle.textContent = 'Editing ' + (data.name || 'position');
        }

        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-edit-course-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openModal({
                id: this.dataset.id,
                name: this.dataset.name,
                departmentId: this.dataset.departmentId,
                fieldOfStudy: this.dataset.fieldOfStudy,
                status: this.dataset.status,
                description: this.dataset.description,
                requirements: this.dataset.requirements,
                universityId: this.dataset.universityId,
                action: this.dataset.action
            });
        });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });
});
</script>
@endsection
