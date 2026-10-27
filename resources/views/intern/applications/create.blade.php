@extends('intern.layout')

@section('title', 'Internship Application - ' . $course->name)

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Internship Application</h2>
            <p>Complete and submit your application for the {{ $course->name }} position.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('dashboard.intern') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Dashboard</span>
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

    <form class="create-user-form" method="POST" action="{{ route('intern.courses.apply.store', $course) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <!-- Section 1: Personal Information -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3>Personal Information</h3>
                    <p>Applicant profile and contact credentials</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="applicant_name">Intern's Name <span class="req">*</span></label>
                    <input id="applicant_name" name="applicant_name" value="{{ old('applicant_name', auth()->user()->name) }}" placeholder="e.g. John Doe" disabled style="background-color: #f9fafb; color: #6b7280;">
                </div>

                <div class="form-field">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" value="{{ auth()->user()->email }}" disabled style="background-color: #f9fafb; color: #6b7280;">
                </div>
            </div>
        </div>

        <!-- Section 2: Department & Selected Role (Disabled) -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <div>
                    <h3>Role &amp; Field of Study</h3>
                    <p>Assigned directorate, locked role designation, and eligible discipline</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="department_name">Judiciary Department / Directorate</label>
                    <input id="department_name" type="text" value="{{ $course->department->name ?? 'Judiciary of Kenya' }}" disabled style="background-color: #f9fafb; color: #6b7280;">
                </div>

                <div class="form-field">
                    <label for="selected_role">Selected Role <span class="req">*</span></label>
                    <input id="selected_role" type="text" value="{{ $course->name }}" disabled style="background-color: #f9fafb; color: #6b7280;">
                </div>

                <div class="form-field form-field-full" id="field-of-study-div">
                    <label for="field_of_study">Field of Study <span class="req">*</span></label>
                    <select id="field_of_study" name="field_of_study" {{ $fieldsOfStudy->isNotEmpty() ? 'required' : '' }}>
                        <option value="" disabled {{ old('field_of_study') ? '' : 'selected' }}>-- Select your Field of Study --</option>
                        @foreach ($fieldsOfStudy as $field)
                            <option value="{{ $field }}" @selected(old('field_of_study') === $field)>
                                {{ $field }}
                            </option>
                        @endforeach
                    </select>
                    @if ($fieldsOfStudy->isNotEmpty())
                        <small class="field-hint">Select your discipline from the options configured by HR for this internship role</small>
                    @endif
                    @error('field_of_study')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Academic Institution -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                        <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                    </svg>
                </div>
                <div>
                    <h3>Academic Institution</h3>
                    <p>Select your university or college of study</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field form-field-full">
                    <label for="institution_select">University / Higher Learning Institution <span class="req">*</span></label>
                    <select id="institution_select" name="institution_select" class="searchable-university-select" data-searchable-university="true" required>
                        <option value="" disabled {{ old('institution_select', auth()->user()->school) ? '' : 'selected' }}>
                            -- Select your university or college --
                        </option>
                        <optgroup label="Accredited &amp; Registered Universities">
                            @foreach ($registeredUniversities as $uni)
                                <option value="{{ $uni->name }}" data-location="{{ $uni->location ?? '' }}" @selected(old('institution_select', auth()->user()->school) === $uni->name)>
                                    {{ $uni->name }} @if($uni->location) ({{ $uni->location }}) @endif
                                </option>
                            @endforeach
                        </optgroup>
                        <option value="__other__" data-is-other="true" @selected(old('institution_select') === '__other__')>
                            ➕ Institution not listed? Add yours here...
                        </option>
                    </select>
                    @error('institution_select')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" id="custom_institution_name_box" style="{{ old('institution_select') === '__other__' ? 'display: flex;' : 'display: none;' }}">
                    <label for="custom_institution_name">Institution Name <span class="req">*</span></label>
                    <input type="text" id="custom_institution_name" name="custom_institution_name" value="{{ old('custom_institution_name') }}" placeholder="e.g. Daystar University">
                    @error('custom_institution_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" id="custom_institution_location_box" style="{{ old('institution_select') === '__other__' ? 'display: flex;' : 'display: none;' }}">
                    <label for="custom_institution_location">Campus / Location</label>
                    <input type="text" id="custom_institution_location" name="custom_institution_location" value="{{ old('custom_institution_location') }}" placeholder="e.g. Nairobi Campus">
                    @error('custom_institution_location')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 4: Required Supporting Documents -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div>
                    <h3>Required Supporting Documents</h3>
                    <p>Upload required application letters and certificates for HR review</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="school_recommendation">School Recommendation Letter <span class="req">*</span></label>
                    <input type="file" id="school_recommendation" name="school_recommendation" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="field-hint">PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                    @error('school_recommendation')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="insurance">Insurance Cover / Policy <span class="req">*</span></label>
                    <input type="file" id="insurance" name="insurance" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="field-hint">PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                    @error('insurance')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="cv">Application Letter / CV <span class="req">*</span></label>
                    <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="field-hint">PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                    @error('cv')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('dashboard.intern') }}" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Submit Application</span>
            </button>
        </div>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('institution_select');
        const customNameBox = document.getElementById('custom_institution_name_box');
        const customLocBox = document.getElementById('custom_institution_location_box');
        const customNameInput = document.getElementById('custom_institution_name');

        function toggleCustomSection() {
            if (!selectEl) return;
            const isOther = selectEl.value === '__other__';
            if (customNameBox) customNameBox.style.display = isOther ? 'flex' : 'none';
            if (customLocBox) customLocBox.style.display = isOther ? 'flex' : 'none';
            if (customNameInput) {
                if (isOther) {
                    customNameInput.setAttribute('required', 'required');
                } else {
                    customNameInput.removeAttribute('required');
                }
            }
        }

        if (selectEl) {
            selectEl.addEventListener('change', toggleCustomSection);
            toggleCustomSection();
        }
    });
</script>
@endpush
@endsection
