@extends('hr.layout')

@section('title', 'Add Supervisor')
@section('heading', 'Add Supervisor')

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Register New Supervisor</h2>
            <p>Create a supervisor account to manage and evaluate internship cohorts.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('hr.supervisors.index') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Supervisors</span>
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

    <form class="create-user-form" method="POST" action="{{ route('hr.supervisors.store') }}">
        @csrf

        <!-- Section 1: Personal Profile -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3>Supervisor Profile</h3>
                    <p>Primary identification and official contact information</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="name">Full Name <span class="req">*</span></label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe" required autofocus minlength="2" maxlength="255">
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. supervisor@judiciary.go.ke" required maxlength="255">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Security & Password -->
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
                    <p>Set a secure initial password for this supervisor</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="password">Password <span class="req">*</span></label>
                    <input id="password" type="password" name="password" minlength="8" placeholder="Min. 8 characters" required autocomplete="new-password">
                    <small class="field-hint">Must be at least 8 characters</small>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Confirm Password <span class="req">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" placeholder="Retype password" required autocomplete="new-password">
                    <small class="field-hint">Retype to confirm</small>
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('hr.supervisors.index') }}" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                <span>Create Supervisor</span>
            </button>
        </div>
    </form>
</section>
@endsection
