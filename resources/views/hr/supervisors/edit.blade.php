@extends('hr.layout')

@section('title', 'Edit Supervisor')
@section('heading', 'Edit Supervisor')

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Edit Supervisor Account</h2>
            <p>Update supervisor credentials and account settings.</p>
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

    <form class="create-user-form" method="POST" action="{{ route('hr.supervisors.update', $supervisor) }}">
        @csrf
        @method('PUT')

        <!-- Section 1: Profile Information -->
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
                    <input id="name" name="name" value="{{ old('name', $supervisor->name) }}" placeholder="e.g. John Doe" required autofocus minlength="2" maxlength="255">
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="email">Email Address <span class="req">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $supervisor->email) }}" placeholder="e.g. supervisor@judiciary.go.ke" required maxlength="255">
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
                    <h3>Security & Authentication</h3>
                    <p>Leave password fields blank if you wish to keep the existing password</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password" minlength="8" placeholder="Leave blank to keep unchanged" autocomplete="new-password">
                    <small class="field-hint">Must be at least 8 characters if changing</small>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" placeholder="Retype new password" autocomplete="new-password">
                    <small class="field-hint">Retype to confirm</small>
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('hr.supervisors.index') }}" class="btn-cancel">Cancel</a>
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
