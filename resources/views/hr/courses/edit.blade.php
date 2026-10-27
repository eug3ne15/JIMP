@extends('hr.layout')

@section('title', 'Edit Open Role')
@section('heading', 'Edit Open Role')

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Edit Open Role</h2>
            <p>Update internship role specifications, Judiciary department linkage, and academic eligibility.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('hr.courses.index') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Open Roles</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="hr-errors" role="alert" style="margin-bottom: 1.5rem;">
            <p><strong>Please correct the following errors:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="create-user-form" method="POST" action="{{ route('hr.courses.update', $course) }}">
        @csrf
        @method('PUT')

        <!-- Section 1: Role Identity & Department -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <div>
                    <h3>Role Designation &amp; Department</h3>
                    <p>Primary title, Judiciary directorate, and intake status</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="name">Role Title / Designation <span class="req">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $course->name) }}" placeholder="e.g. Software Development & Systems Intern" required autofocus maxlength="255">
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="department_id">Judiciary Department</label>
                    <select id="department_id" name="department_id">
                        <option value="">-- General / Open Directorate --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(old('department_id', $course->department_id) == $dept->id)>
                                {{ $dept->name }} @if($dept->code) ({{ $dept->code }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-grid-2" style="margin-top: 1rem;">
                <div class="form-field">
                    <label for="field_of_study">Eligible Field of Study <span class="field-hint">(displayed to interns)</span></label>
                    <input id="field_of_study" name="field_of_study" value="{{ old('field_of_study', $course->field_of_study) }}" placeholder="e.g. Computer Science, Software Engineering, IT" maxlength="255">
                    @error('field_of_study')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="status">Intake Status</label>
                    <select id="status" name="status">
                        <option value="open" @selected(old('status', $course->status) === 'open')>Open for Intake</option>
                        <option value="closed" @selected(old('status', $course->status) === 'closed')>Closed</option>
                    </select>
                    @error('status')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Scope & Requirements -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3>Scope &amp; Academic Requirements</h3>
                    <p>Details displayed on the role card and flashcard modal</p>
                </div>
            </div>

            <div class="form-field">
                <label for="description">Role Scope &amp; Responsibilities</label>
                <textarea id="description" name="description" rows="3" placeholder="Describe the responsibilities and learning opportunities during the placement...">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field" style="margin-top: 1rem;">
                <label for="requirements">Academic &amp; Eligibility Requirements</label>
                <textarea id="requirements" name="requirements" rows="2" placeholder="e.g. Enrolled in an accredited degree/diploma in related discipline, Dean recommendation letter...">{{ old('requirements', $course->requirements) }}</textarea>
                @error('requirements')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field" style="margin-top: 1rem;">
                <label for="university_id">Associated Institution <span class="field-hint">(optional restriction)</span></label>
                <select id="university_id" name="university_id">
                    <option value="">-- General / Open to All Institutions --</option>
                    @foreach ($universities as $uni)
                        <option value="{{ $uni->id }}" @selected(old('university_id', $course->university_id) == $uni->id)>
                            {{ $uni->name }}
                        </option>
                    @endforeach
                </select>
                @error('university_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Section 3: Program Stats & Cohorts -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h3>Associated Enrollments</h3>
                    <p>Current active internships linked to this role</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label>Active Internships</label>
                    <input type="text" value="{{ $course->internships()->count() }} active cohort(s)" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label>Candidate Applications</label>
                    <input type="text" value="{{ $course->applications()->count() }} submitted application(s)" disabled style="background: #f1f5f9; color: #475569;">
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('hr.courses.index') }}" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</section>
@endsection
