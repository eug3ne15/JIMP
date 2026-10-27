@extends('admin.layout')

@section('title', 'Create User')
@section('heading', 'Create User')

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Create New User</h2>
            <p>Add a user account and configure system access and credentials.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('admin.users.index') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Users</span>
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

    <form class="create-user-form" method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <!-- Section 1: Account Information -->
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
                    <p>Basic user profile and identification details</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="name">Full Name <span class="req">*</span></label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required autofocus>
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. john@example.com" required>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Role & Educational Assignment -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <div>
                    <h3>Role & Assignment</h3>
                    <p>Select portal access level and institutional background</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field form-field-full">
                    <label for="role">User Role <span class="req">*</span></label>
                    <select id="role" name="role" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role', 'intern') === $role->name)>
                                {{ $role->label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" id="university-field" @if (old('role', 'intern') !== 'intern') style="display: none;" @endif>
                    <label for="school">University / Institution <span class="req">*</span></label>
                    <input type="text" id="school" name="school" value="{{ old('school') }}" placeholder="e.g. Egerton University" @disabled(old('role', 'intern') !== 'intern')>
                    @error('school')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" id="course-field" @if (old('role', 'intern') !== 'intern') style="display: none;" @endif>
                    <label for="course">Course of Study <span class="req">*</span></label>
                    <input type="text" id="course" name="course" value="{{ old('course') }}" placeholder="e.g. Bachelor of Laws (LL.B)" @disabled(old('role', 'intern') !== 'intern')>
                    @error('course')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Security & Credentials -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div>
                    <h3>Security Credentials</h3>
                    <p>Create a secure password for this user account</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="password">Password <span class="req">*</span></label>
                    <input id="password" type="password" name="password" placeholder="Min. 4 characters" required autocomplete="new-password">
                    <small class="field-hint">Must be 4 to 10 characters</small>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Confirm Password <span class="req">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter password" required autocomplete="new-password">
                    <small class="field-hint">Retype to confirm</small>
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('admin.users.index') }}" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>Create User</span>
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleField = document.getElementById('role');
        const universityField = document.getElementById('university-field');
        const courseField = document.getElementById('course-field');

        function toggleInternFields() {
            const isIntern = roleField && roleField.value === 'intern';
            if (universityField) {
                universityField.style.display = isIntern ? 'flex' : 'none';
                const input = universityField.querySelector('input');
                if (input) input.disabled = !isIntern;
            }
            if (courseField) {
                courseField.style.display = isIntern ? 'flex' : 'none';
                const input = courseField.querySelector('input');
                if (input) input.disabled = !isIntern;
            }
        }

        if (roleField) {
            roleField.addEventListener('change', toggleInternFields);
            toggleInternFields();
        }
    });
</script>
@endsection
