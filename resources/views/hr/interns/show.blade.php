@extends('hr.layout')

@section('title', 'Application Details')
@section('heading', 'Application Details: ' . ($intern->applicant_name ?? $intern->user->name))

@section('content')
<section class="admin-panel create-user-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Application Details: {{ $intern->applicant_name ?? $intern->user->name }}</h2>
            <p>Review candidate academic credentials, update vetting decision, and manage evaluation reports.</p>
        </div>
        <a class="admin-btn-back" href="{{ route('hr.intern.index') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Back to Applications</span>
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

    <form class="create-user-form" method="POST" action="{{ route('hr.intern.update', $intern) }}">
        @csrf
        @method('PUT')

        <!-- Section 1: Candidate Profile & Academic Background -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3>Applicant Information</h3>
                    <p>Candidate identification and educational qualifications</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="applicant_name">Applicant Full Name <span class="req">*</span></label>
                    <input id="applicant_name" name="applicant_name" value="{{ old('applicant_name', $intern->applicant_name ?? $intern->user->name) }}" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label>Email Address</label>
                    <input type="email" value="{{ $intern->user->email }}" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label>Course / Position Applied For</label>
                    <input type="text" value="{{ $intern->course?->name ?? $intern->internship?->course?->name ?? 'N/A' }}" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label for="institution">Higher Learning Institution / University</label>
                    <input id="institution" name="institution" list="showRegisteredUnisList" value="{{ old('institution', $intern->institution ?? $intern->user->school) }}" placeholder="e.g. University of Nairobi" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label>Intern's Field of Study</label>
                    <input type="text" value="{{ $intern->user->course ?? 'Not provided' }}" disabled style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="form-field">
                    <label>Submission Date</label>
                    <input type="text" value="{{ $intern->created_at->format('M j, Y H:i') }}" disabled style="background: #f1f5f9; color: #475569;">
                </div>
            </div>

            {{-- Supporting Application Documents --}}
            <div class="hr-docs-review-box">
                <div class="hr-docs-header">
                    <h4>Submitted Supporting Documents</h4>
                    <span class="hr-docs-subtitle">Verify the candidate's recommendation letter, insurance cover, and CV</span>
                </div>

                <div class="hr-docs-grid">
                    {{-- 1. School Recommendation Letter --}}
                    <div class="hr-doc-item {{ $intern->school_recommendation_path ? 'doc-present' : 'doc-missing' }}">
                        <div class="hr-doc-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <div class="hr-doc-details">
                            <span class="hr-doc-name">School Recommendation Letter</span>
                            @if ($intern->school_recommendation_path)
                                <span class="hr-doc-status status-uploaded">✓ Document Attached</span>
                            @else
                                <span class="hr-doc-status status-missing">Not Provided</span>
                            @endif
                        </div>
                        @if ($intern->school_recommendation_path)
                            <div class="hr-doc-actions">
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'school_recommendation', 'action' => 'open']) }}" class="btn-doc-open" target="_blank" title="Open and preview School Recommendation Letter">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>Open</span>
                                </a>
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'school_recommendation', 'action' => 'download']) }}" class="btn-doc-download" target="_blank" title="Download School Recommendation Letter">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Insurance Cover --}}
                    <div class="hr-doc-item {{ $intern->insurance_path ? 'doc-present' : 'doc-missing' }}">
                        <div class="hr-doc-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="hr-doc-details">
                            <span class="hr-doc-name">Insurance Cover / Policy</span>
                            @if ($intern->insurance_path)
                                <span class="hr-doc-status status-uploaded">✓ Document Attached</span>
                            @else
                                <span class="hr-doc-status status-missing">Not Provided</span>
                            @endif
                        </div>
                        @if ($intern->insurance_path)
                            <div class="hr-doc-actions">
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'insurance', 'action' => 'open']) }}" class="btn-doc-open" target="_blank" title="Open and preview Insurance Cover">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>Open</span>
                                </a>
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'insurance', 'action' => 'download']) }}" class="btn-doc-download" target="_blank" title="Download Insurance Cover">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Application Letter / CV --}}
                    <div class="hr-doc-item {{ $intern->cv_path ? 'doc-present' : 'doc-missing' }}">
                        <div class="hr-doc-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                <path d="M9 12h6"></path>
                                <path d="M9 16h6"></path>
                            </svg>
                        </div>
                        <div class="hr-doc-details">
                            <span class="hr-doc-name">Application Letter / CV</span>
                            @if ($intern->cv_path)
                                <span class="hr-doc-status status-uploaded">✓ Document Attached</span>
                            @else
                                <span class="hr-doc-status status-missing">Not Provided</span>
                            @endif
                        </div>
                        @if ($intern->cv_path)
                            <div class="hr-doc-actions">
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'cv', 'action' => 'open']) }}" class="btn-doc-open" target="_blank" title="Open and preview Application Letter / CV">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>Open</span>
                                </a>
                                <a href="{{ route('hr.intern.download', [$intern, 'type' => 'cv', 'action' => 'download']) }}" class="btn-doc-download" target="_blank" title="Download Application Letter / CV">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    @if (!$intern->school_recommendation_path && !$intern->insurance_path && !$intern->cv_path && $intern->document_path)
                        {{-- Legacy single document fallback --}}
                        <div class="hr-doc-item doc-present">
                            <div class="hr-doc-icon">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <div class="hr-doc-details">
                                <span class="hr-doc-name">Attached Supporting Document</span>
                                <span class="hr-doc-status status-uploaded">✓ Legacy File Uploaded</span>
                            </div>
                            <div class="hr-doc-actions">
                                <a href="{{ route('hr.intern.download', [$intern, 'action' => 'open']) }}" class="btn-doc-open" target="_blank">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>Open</span>
                                </a>
                                <a href="{{ route('hr.intern.download', [$intern, 'action' => 'download']) }}" class="btn-doc-download" target="_blank">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Download</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 2: Vetting Decision & Qualification -->
        <div class="form-section">
            <div class="form-section-header">
                <div class="form-section-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <div>
                    <h3>Vetting Decision & Assessment</h3>
                    <p>Determine candidate qualification and update stage</p>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <label for="status">Application Status <span class="req">*</span></label>
                    <select id="status" name="status" required>
                        <option value="pending" @selected($intern->status === 'pending')>Pending Review</option>
                        <option value="shortlisted" @selected($intern->status === 'shortlisted')>Shortlisted</option>
                        <option value="accepted" @selected($intern->status === 'accepted')>Accepted</option>
                        <option value="info_requested" @selected($intern->status === 'info_requested')>Request More Info</option>
                        <option value="rejected" @selected($intern->status === 'rejected')>Rejected</option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="is_qualified">Qualification Metric</label>
                    <select id="is_qualified" name="is_qualified">
                        <option value="" @selected(is_null($intern->is_qualified))>— Not Rated —</option>
                        <option value="1" @selected($intern->is_qualified === true)>Yes (Meets Requirements)</option>
                        <option value="0" @selected($intern->is_qualified === false)>No (Does Not Meet Requirements)</option>
                    </select>
                    <small class="field-hint">Tags the candidate for internal reporting</small>
                </div>

                <div class="form-field">
                    <label for="supervisor_id">Assign Placement Supervisor</label>
                    <select id="supervisor_id" name="supervisor_id">
                        <option value="">— Unassigned —</option>
                        @foreach ($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" @selected(old('supervisor_id', $intern->internship?->supervisor_id) == $supervisor->id)>
                                {{ $supervisor->name }} ({{ $supervisor->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="field-hint">Assigning a supervisor grants them access to evaluate this intern</small>
                </div>

                <div class="form-field form-field-full">
                    <label for="hr_comment">HR Comments / Checklist Notes</label>
                    <textarea id="hr_comment" name="hr_comment" rows="2" placeholder="Candidate strengths, document verification notes, or extra info requested...">{{ old('hr_comment', $intern->hr_comment) }}</textarea>
                </div>

                <div class="form-field form-field-full" id="showRejectionReasonGroup" style="{{ $intern->status === 'rejected' ? 'display: flex;' : 'display: none;' }}">
                    <label for="rejection_reason">Reason for Rejection</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="2" placeholder="Explain basis for disqualification (shared with candidate)...">{{ old('rejection_reason', $intern->rejection_reason) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div class="form-actions-bar">
            <a href="{{ route('hr.intern.index') }}" class="btn-cancel">Back</a>
            <button class="btn-submit" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Save Decision & Notify</span>
            </button>
        </div>
    </form>
</section>

<!-- Section 3: Supervisor Evaluation & Recommendation Letter -->
<section class="admin-panel create-user-panel" style="margin-top: 1.5rem;">
    <div class="admin-panel-heading">
        <div>
            <h2>Supervisor Evaluation & Recommendation Letter</h2>
            <p>Review the supervisor's performance evaluation and issue the official recommendation letter.</p>
        </div>
    </div>

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
                <h3>Supervisor Evaluation Report</h3>
                <p>Status of evaluation submitted by internship supervisor</p>
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            @if ($intern->internship?->supervisor)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; margin-bottom: 0.75rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 0.88rem; color: #166534;">
                    <div>
                        <strong>Assigned Supervisor:</strong> {{ $intern->internship->supervisor->name }} ({{ $intern->internship->supervisor->email }})
                    </div>
                    <span class="intern-tab-count" style="background: #dcfce7; color: #15803d; font-weight: 700;">Active Placement</span>
                </div>
            @else
                <div style="padding: 0.75rem 1rem; margin-bottom: 0.75rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 6px; font-size: 0.88rem; color: #92400e;">
                    <strong>Supervisor Assignment:</strong> No supervisor assigned yet. Select a supervisor in Section 2 above and click "Save Decision & Notify" to activate supervisor evaluation.
                </div>
            @endif

            @if ($intern->supervisor_evaluation_path)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--border-radius-sm);">
                    <span style="color: #15803d; font-weight: 600;">✓ Supervisor evaluation received</span>
                    <a class="admin-primary-link" href="{{ route('hr.intern.evaluation.download', $intern) }}" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download Evaluation Report
                    </a>
                </div>
            @else
                <div style="padding: 0.85rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--border-radius-sm); color: #64748b; font-size: 0.9rem;">
                    The supervisor has not submitted an evaluation report yet for this candidate.
                </div>
            @endif
        </div>

        <form class="create-user-form" method="POST" action="{{ route('hr.intern.recommendation.issue', $intern) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid-2">
                <div class="form-field">
                    <label for="recommendation_letter">Recommendation Letter (PDF) <span class="req">*</span></label>
                    <input id="recommendation_letter" type="file" name="recommendation_letter" accept="application/pdf,.pdf" required>
                    <small class="field-hint">Upload signed PDF recommendation letter</small>
                </div>

                <div class="form-field" style="justify-content: flex-end;">
                    <label style="visibility: hidden;">Action</label>
                    <button class="btn-submit" type="submit" style="width: fit-content;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <polyline points="16 16 12 12 8 16"></polyline>
                            <line x1="12" y1="12" x2="12" y2="21"></line>
                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                        </svg>
                        <span>{{ $intern->recommendation_letter_path ? 'Replace Recommendation Letter' : 'Issue Recommendation Letter' }}</span>
                    </button>
                </div>
            </div>
        </form>

        @if ($intern->recommendation_letter_path)
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: var(--border-radius-sm); color: #065f46; font-size: 0.85rem; font-weight: 500;">
                ✓ Official recommendation letter has been issued. The intern can download it from their dashboard.
            </div>
        @endif
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status');
    const rejGroup = document.getElementById('showRejectionReasonGroup');

    if (statusSelect && rejGroup) {
        statusSelect.addEventListener('change', function () {
            rejGroup.style.display = this.value === 'rejected' ? 'flex' : 'none';
        });
    }
});
</script>
@endsection
