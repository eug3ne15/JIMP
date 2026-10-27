<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judiciary Internship Management Portal - Open Roles</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page">
    <div class="welcome-container">
        <!-- Top Judiciary Brand Navigation Header -->
        <header class="welcome-navbar">
            <div class="welcome-brand">
                <img src="{{ asset('header_banner.png') }}" alt="Judiciary of Kenya" class="welcome-logo">
                <div class="welcome-brand-text">
                    <span class="welcome-portal-badge">Judiciary Internship Management Portal (JIMP)</span>
                </div>
            </div>
            <div class="welcome-nav-actions">
                <a href="{{ route('login') }}" class="btn-welcome-login">Login to Portal</a>
                <a href="{{ route('register') }}" class="btn-welcome-register">Register as Intern</a>
            </div>
        </header>
        <!-- Open Roles Grid -->
        <section class="welcome-roles-section">
            <div class="roles-grid" id="rolesGrid">
                @forelse ($courses as $role)
                    <article class="role-preview-card js-role-card"
                        data-dept-id="{{ $role->department_id ?? 'none' }}"
                        data-role-name="{{ $role->name }}"
                        data-dept-name="{{ $role->department?->name ?? 'Judiciary of Kenya' }}"
                        data-field-study="{{ $role->field_of_study ?? 'Open to all academic disciplines' }}"
                        data-desc="{{ $role->description ?? 'Gain practical experience supporting judicial administration and proceedings under experienced mentors.' }}"
                        data-reqs="{{ $role->requirements ?? 'Enrolled in an accredited diploma or degree programme with recommendation letter from Dean/HOD.' }}"
                    >
                        <div class="role-card-top">
                            <span class="role-dept-tag">
                                {{ $role->department?->name ?? 'Judiciary Directorate' }}
                            </span>
                            <span class="role-open-status">Open Intake</span>
                        </div>
                        <h4 class="role-card-title">{{ $role->name }}</h4>
                        <p class="role-card-desc">
                            {{ str($role->description ?? 'Support judicial operations and enhance your career readiness with experienced mentors.')->limit(120) }}
                        </p>
                        <div class="role-card-footer">
                            <span class="btn-card-learn-more">
                                View Details &rarr;
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="welcome-empty-state">
                        <p>No open roles are currently published. Please check back shortly for upcoming intakes.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Interactive Role Flashcard Modal -->
    <div id="roleFlashcardModal" class="admin-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="flashcardTitle">
        <div class="admin-modal-dialog flashcard-dialog">
            <div class="flashcard-badge-strip">
                <span id="flashcardDeptBadge" class="flashcard-dept">Judiciary Department</span>
                <span class="flashcard-status-badge">Active Internship Opportunity</span>
            </div>

            <div class="admin-modal-header flashcard-header">
                <div>
                    <h2 id="flashcardTitle">Role Title</h2>
                    <p id="flashcardSubtitle" class="flashcard-sub">Judiciary Internship Programme</p>
                </div>
                <button type="button" class="admin-modal-close" id="closeFlashcardBtn" aria-label="Close dialog">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="flashcard-body">
                <!-- Field of Study Section -->
                <div class="flashcard-section flashcard-highlight-box">
                    <div class="flashcard-icon-wrap">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="section-eyebrow">Required / Recommended Field of Study</span>
                        <strong id="flashcardFieldStudy" class="flashcard-major">Field of Study</strong>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="flashcard-section">
                    <h4>Internship Scope &amp; Responsibilities</h4>
                    <p id="flashcardDescription" class="flashcard-text">Detailed description will appear here.</p>
                </div>

                <!-- Requirements Section -->
                <div class="flashcard-section">
                    <h4>Candidate Requirements &amp; Eligibility</h4>
                    <p id="flashcardRequirements" class="flashcard-text">Requirements will appear here.</p>
                </div>

                <!-- Guidance note -->
                <div class="flashcard-notice">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>To apply for this role, log in with your intern account or create a new student profile on the portal.</span>
                </div>
            </div>

            <!-- Modal Action Footer -->
            <div class="admin-modal-actions flashcard-actions">
                <button type="button" class="admin-cancel-btn" id="btnDismissFlashcard">Close</button>
                <a href="{{ route('login') }}" class="admin-submit-btn btn-flashcard-apply" id="btnFlashcardApply">
                    <span>Apply for this Role &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
