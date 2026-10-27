@extends('hr.layout')

@section('title', 'Edit University')
@section('heading', 'Edit University')

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Edit University: {{ $university->name }}</h2>
            <p>Update institution details and accreditation approval status.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('hr.universities.index') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Universities</span>
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

    @if ($university->requester)
        <div class="uni-requester-alert" style="margin-bottom: 1.5rem;">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <strong>Requested by Intern:</strong> {{ $university->requester->name }} ({{ $university->requester->email }})
                <span class="requester-timestamp">&bull; Submitted {{ $university->created_at?->diffForHumans() }}</span>
            </div>
        </div>
    @endif

    <form class="create-user-form" method="POST" action="{{ route('hr.universities.update', $university) }}">
        @csrf
        @method('PUT')

        <!-- Section 1: Institution Details -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M2 8h20M12 2l10 6H2z"></path>
                        <path d="M6 8v11M10 8v11M14 8v11M18 8v11"></path>
                    </svg>
                </div>
                <div>
                    <h3>Institution Details</h3>
                    <p>Official name, campus location, and accreditation status</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="name">University / Institution Name <span class="req">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $university->name) }}" required maxlength="255">
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="location">Campus / Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $university->location) }}" placeholder="e.g. Main Campus, Nairobi, Kenya" maxlength="255">
                    @error('location')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="status">Accreditation Status <span class="req">*</span></label>
                    <select id="status" name="status" required>
                        <option value="registered" @selected(old('status', $university->status) === 'registered')>
                            Registered / Approved (Available to Interns)
                        </option>
                        <option value="pending" @selected(old('status', $university->status) === 'pending')>
                            Pending Approval (Awaiting HR Confirmation)
                        </option>
                    </select>
                    @error('status')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('hr.universities.index') }}" class="btn-cancel">Cancel</a>
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
