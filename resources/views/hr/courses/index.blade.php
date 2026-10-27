@extends('hr.layout')
@section('title', 'Open Positions Directory')
@section('heading', 'Open Positions Directory')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Judiciary Open Positions Directory</h2>
            <p>{{ $courses->total() }} position{{ $courses->total() === 1 ? '' : 's' }} registered &bull; {{ $totalApplications }} applicant{{ $totalApplications === 1 ? '' : 's' }} across all roles &bull; Click any card to manage fields of study and statistics</p>
        </div>
        <a class="admin-primary-link" href="{{ route('hr.courses.create') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.35rem; vertical-align: -2px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Add Open Position
        </a>
    </div>

    {{-- Filter Toolbar --}}
    <div class="hr-table-filter-bar">
        <form method="GET" action="{{ route('hr.courses.index') }}" class="hr-filter-form">
            <div class="hr-filter-fields">
                <div class="filter-input-wrap">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search position or field of study..."
                        class="admin-input-filter"
                    >
                </div>

                <select name="department_id" class="admin-select-filter" onchange="this.form.submit()">
                    <option value="">-- All Departments --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="admin-btn-filter">Filter</button>
                @if (request()->hasAny(['search', 'department_id']))
                    <a href="{{ route('hr.courses.index') }}" class="admin-btn-clear-filter">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Positions Cards Grid --}}
    <div class="hr-roles-cards-grid">
        @forelse ($courses as $course)
            <article class="hr-role-card">
                <div>
                    <div class="hr-role-card-header">
                        <span class="hr-dept-badge">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M3 9h18"></path>
                                <path d="M9 21V9"></path>
                            </svg>
                            {{ $course->department?->name ?? 'General Directorate' }}
                        </span>

                        @if ($course->isOpen())
                            <span class="hr-intake-badge is-open">Open Intake</span>
                        @else
                            <span class="hr-intake-badge is-closed">Closed</span>
                        @endif
                    </div>

                    <h3 class="hr-role-card-title">
                        <a href="{{ route('hr.courses.show', $course) }}" title="View statistics & manage fields of study">
                            {{ $course->name }}
                        </a>
                    </h3>

                    {{-- Statistics Strip on Card --}}
                    <div class="hr-role-stats-strip">
                        @if ($course->fields_of_study_count > 0)
                            <span class="hr-stat-pill stat-fields" title="Configured Fields of Study">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                                {{ $course->fields_of_study_count }} Field{{ $course->fields_of_study_count === 1 ? '' : 's' }} of Study
                            </span>
                        @else
                            <a href="{{ route('hr.courses.show', $course) }}" class="hr-stat-pill stat-fields-none" title="No fields of study configured yet. Click to add.">
                                ⚠️ 0 Fields of Study
                            </a>
                        @endif

                        <a href="{{ route('hr.intern.index', ['course_id' => $course->id]) }}" class="hr-stat-pill stat-apps" title="View candidates filtered in Interns management">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            {{ $course->applications_count }} applicant{{ $course->applications_count === 1 ? '' : 's' }}
                        </a>

                        @if ($course->internships_count > 0)
                            <span class="hr-stat-pill stat-placements" title="Assigned Intern Placements">
                                {{ $course->internships_count }} Placement{{ $course->internships_count === 1 ? '' : 's' }}
                            </span>
                        @endif
                    </div>

                    {{-- Fields of Study Preview List --}}
                    <div class="hr-role-fields-preview">
                        <span class="hr-fields-preview-label">Eligible Fields of Study:</span>
                        <div class="hr-fields-chips-list">
                            @forelse ($course->fields_of_study_list as $idx => $f)
                                @if ($idx < 3)
                                    @php $fCount = $course->getFieldOfStudyApplicationsCount($f); @endphp
                                    <span class="hr-field-chip">
                                        {{ $f }}
                                        @if ($fCount > 0)
                                            <strong style="margin-left: 0.25rem; color: #025532; font-size: 0.75rem;" title="{{ $fCount }} applicant(s)">({{ $fCount }})</strong>
                                        @endif
                                    </span>
                                @elseif ($idx === 3)
                                    <span class="hr-field-chip-more">+{{ $course->fields_of_study_count - 3 }} more</span>
                                @endif
                            @empty
                                <a href="{{ route('hr.courses.show', $course) }}" class="hr-field-chip-add" style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; font-weight: 600; color: #047857; background: #ecfdf5; border: 1px dashed #10b981; padding: 0.2rem 0.55rem; border-radius: 9999px; text-decoration: none;" title="Open role details to add eligible disciplines">
                                    + Add Field of Study
                                </a>
                            @endforelse
                        </div>
                    </div>

                    @if ($course->description)
                        <p style="font-size: 0.82rem; color: #64748b; line-height: 1.45; margin: 0 0 1rem;">
                            {{ str($course->description)->limit(100) }}
                        </p>
                    @endif
                </div>

                <div class="hr-role-card-footer">
                    <a href="{{ route('hr.courses.show', $course) }}" class="btn-card-manage-role">
                        <span>Manage Fields &amp; Stats</span>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>

                    <div class="card-quick-actions">
                        <button
                            type="button"
                            class="btn-card-action btn-card-edit js-edit-course-btn"
                            data-id="{{ $course->id }}"
                            data-name="{{ $course->name }}"
                            data-department-id="{{ $course->department_id ?? '' }}"
                            data-field-of-study="{{ $course->field_of_study ?? '' }}"
                            data-status="{{ $course->status ?? 'open' }}"
                            data-description="{{ $course->description ?? '' }}"
                            data-requirements="{{ $course->requirements ?? '' }}"
                            data-university-id="{{ $course->university_id ?? '' }}"
                            data-action="{{ route('hr.courses.update', $course) }}"
                            title="Edit position"
                            aria-label="Edit position"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span>Edit</span>
                        </button>

                        <form method="POST" action="{{ route('hr.courses.destroy', $course) }}" class="inline-action-form" style="display: inline; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none; width: auto;" onsubmit="return confirm('Delete position \'{{ addslashes($course->name) }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-card-action btn-card-delete" type="submit" title="Delete position" aria-label="Delete position">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="admin-empty" style="grid-column: 1 / -1; padding: 3rem 1.5rem; text-align: center;">
                No open positions match the criteria or have been created yet.
            </div>
        @endforelse
    </div>

    <div class="admin-pagination">{{ $courses->links() }}</div>
</section>

{{-- Edit Open Position Modal --}}
<div id="editCourseModal" class="admin-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalCourseTitle">
    <div class="admin-modal-dialog" style="max-width: 620px;">
        <div class="admin-modal-header">
            <div>
                <h3 id="modalCourseTitle">Edit Open Position</h3>
                <p id="modalCourseSubtitle" class="admin-field-hint">Update position designation, department, and field of study</p>
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
                <label for="modal_course_name">Position Designation / Title <span style="color: #b91c1c;">*</span></label>
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

        if (data.focusField && fieldInput) {
            setTimeout(() => fieldInput.focus(), 80);
        }
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
                action: this.dataset.action,
                focusField: this.dataset.focusField === '1'
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
