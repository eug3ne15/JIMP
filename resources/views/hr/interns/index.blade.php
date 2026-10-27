@extends('hr.layout')
@section('title', 'Intern Applications')
@section('heading', 'Intern Applications')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Submitted Intern Applications</h2>
            <p>
                @if ($selectedCourse)
                    Filtered to <strong>{{ $selectedCourse->name }}</strong> &bull; {{ $applications->total() }} applicant{{ $applications->total() === 1 ? '' : 's' }} found
                @else
                    {{ $applications->total() }} applications pending evaluation and vetting across all courses
                @endif
            </p>
        </div>
        @if ($selectedCourse)
            <a class="admin-primary-link" href="{{ route('hr.intern.index') }}">
                View All Applications
            </a>
        @endif
    </div>

    {{-- Filter & Search Toolbar --}}
    <form method="GET" action="{{ route('hr.intern.index') }}" class="hr-filter-toolbar">
        <div class="filter-field filter-search">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email...">
        </div>
        <div class="filter-field">
            <select name="course_id" onchange="this.form.submit()">
                <option value="">All Courses / Positions</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="shortlisted" @selected(request('status') === 'shortlisted')>Shortlisted</option>
                <option value="info_requested" @selected(request('status') === 'info_requested')>Info Requested</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
        </div>
        <button type="submit" class="admin-primary-link btn-filter">Filter</button>
        @if (request()->hasAny(['search', 'course_id', 'status', 'institution', 'date']))
            <a href="{{ route('hr.intern.index') }}" class="btn-clear-filters">Reset</a>
        @endif
    </form>

    @if ($selectedCourse)
        <div class="filter-active-pill">
            <div class="filter-pill-text">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                <span>Showing applicants for course: <strong>{{ $selectedCourse->name }}</strong> ({{ $applications->total() }} candidate{{ $applications->total() === 1 ? '' : 's' }})</span>
            </div>
            <a href="{{ route('hr.intern.index') }}" class="filter-pill-remove" title="Remove course filter">
                <span>Show All Courses</span> &times;
            </a>
        </div>
    @endif

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Course / Position</th>
                    <th>Institution</th>
                    <th>Documents</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Qualified</th>
                    <th>Supervisor</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $app)
                    <tr>
                        <td data-label="Applicant">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: rgba(2, 85, 50, 0.08); color: var(--judiciary-deep-green); flex-shrink: 0;">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <strong style="color: var(--judiciary-text); display: block;">{{ $app->applicant_name ?? $app->user->name }}</strong>
                                    <span class="hr-table-subtitle" style="font-size: 0.78rem; color: #64748b;">{{ $app->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Course / Position">
                            @if ($app->course)
                                <a href="{{ route('hr.intern.index', ['course_id' => $app->course_id]) }}" class="table-course-link" title="Filter by {{ $app->course->name }}">
                                    {{ $app->course->name }}
                                </a>
                            @elseif ($app->internship?->course)
                                <a href="{{ route('hr.intern.index', ['course_id' => $app->internship->course_id]) }}" class="table-course-link" title="Filter by {{ $app->internship->course->name }}">
                                    {{ $app->internship->course->name }}
                                </a>
                            @else
                                <span style="font-weight: 500; color: #64748b;">{{ $app->user->course ?? '—' }}</span>
                            @endif
                        </td>
                        <td data-label="Institution">
                            <span style="color: #475569;">{{ $app->institution ?? $app->user->school ?? '—' }}</span>
                        </td>
                        <td data-label="Documents">
                            <div class="intern-docs-pills">
                                @if ($app->school_recommendation_path)
                                    <a href="{{ route('hr.intern.download', [$app, 'type' => 'school_recommendation', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open School Recommendation Letter">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <span>School Rec</span>
                                    </a>
                                @endif

                                @if ($app->insurance_path)
                                    <a href="{{ route('hr.intern.download', [$app, 'type' => 'insurance', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Insurance Policy">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        </svg>
                                        <span>Insurance</span>
                                    </a>
                                @endif

                                @if ($app->cv_path)
                                    <a href="{{ route('hr.intern.download', [$app, 'type' => 'cv', 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Application Letter / CV">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                        </svg>
                                        <span>CV / Letter</span>
                                    </a>
                                @endif

                                @if (!$app->school_recommendation_path && !$app->insurance_path && !$app->cv_path)
                                    @if ($app->document_path)
                                        <a href="{{ route('hr.intern.download', [$app, 'action' => 'open']) }}" class="doc-pill-link" target="_blank" title="Open Attached Document">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            </svg>
                                            <span>Doc</span>
                                        </a>
                                    @else
                                        <span class="text-muted" style="font-size: 0.78rem; color: #94a3b8;">None</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td data-label="Applied On">
                            <span style="font-size: 0.85rem; color: #64748b;">{{ $app->created_at->format('M j, Y') }}</span>
                        </td>
                        <td data-label="Status">
                            <span class="hr-status hr-status-{{ $app->status }}">
                                {{ str($app->status)->replace('_', ' ')->title() }}
                            </span>
                        </td>
                        <td data-label="Qualified">
                            @if (is_null($app->is_qualified))
                                <span class="intern-tab-count" style="background: #f1f5f9; color: #64748b;">—</span>
                            @elseif ($app->is_qualified)
                                <span class="intern-tab-count" style="background: #dcfce7; color: #15803d; font-weight: 700;">Yes</span>
                            @else
                                <span class="intern-tab-count" style="background: #fee2e2; color: #991b1b; font-weight: 700;">No</span>
                            @endif
                        </td>
                        <td data-label="Supervisor">
                            @if ($app->internship?->supervisor)
                                <span class="intern-tab-count" style="background: #eff6ff; color: #1d4ed8; font-weight: 600; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    👤 {{ $app->internship->supervisor->name }}
                                </span>
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Unassigned</span>
                            @endif
                        </td>
                        <td data-label="Actions" style="text-align: right;">
                            <div class="admin-actions" style="justify-content: flex-end;">
                                <a class="admin-edit-link" href="{{ route('hr.intern.show', $app) }}">
                                    View Details
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td class="admin-empty" colspan="9">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="admin-pagination">{{ $applications->links() }}</div>
</section>
@endsection
