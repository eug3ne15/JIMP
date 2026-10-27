@extends('hr.layout')
@section('title', 'Supervisors')
@section('heading', 'Supervisors')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Supervisor Directory</h2>
            <p>{{ $supervisors->total() }} supervisor accounts managing intern cohorts</p>
        </div>
        <a class="admin-primary-link" href="{{ route('hr.supervisors.create') }}">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.35rem; vertical-align: -2px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Add supervisor
        </a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Supervisor</th>
                    <th>Email Address</th>
                    <th>Supervised Cohorts</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($supervisors as $supervisor)
                    <tr>
                        <td data-label="Supervisor">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: rgba(2, 85, 50, 0.08); color: var(--judiciary-deep-green); flex-shrink: 0;">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                                <span style="font-weight: 600; color: var(--judiciary-text);">{{ $supervisor->name }}</span>
                            </div>
                        </td>
                        <td data-label="Email Address">
                            <span style="color: #475569;">{{ $supervisor->email }}</span>
                        </td>
                        <td data-label="Supervised Cohorts">
                            <span class="intern-tab-count" style="background: #f1f5f9; color: #334155; font-weight: 700; padding: 0.2rem 0.6rem;">
                                {{ $supervisor->supervised_internships_count }} internship(s)
                            </span>
                        </td>
                        <td data-label="Actions" style="text-align: right;">
                            <div class="admin-actions" style="justify-content: flex-end;">
                                <button
                                    type="button"
                                    class="admin-edit-link js-edit-supervisor-btn"
                                    data-id="{{ $supervisor->id }}"
                                    data-name="{{ $supervisor->name }}"
                                    data-email="{{ $supervisor->email }}"
                                    data-action="{{ route('hr.supervisors.update', $supervisor) }}"
                                >
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('hr.supervisors.destroy', $supervisor) }}" onsubmit="return confirm('Delete supervisor account \'{{ addslashes($supervisor->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="admin-delete-button" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="admin-empty" colspan="4">No supervisors have been registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $supervisors->links() }}</div>
</section>

{{-- Edit Supervisor Modal (Matches Admin Edit User Modal) --}}
<div id="editSupervisorModal" class="admin-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modalSupervisorTitle">
    <div class="admin-modal-dialog">
        <div class="admin-modal-header">
            <div>
                <h3 id="modalSupervisorTitle">Edit Supervisor</h3>
                <p id="modalSupervisorSubtitle" class="admin-field-hint">Update profile information and password</p>
            </div>
            <button type="button" class="admin-modal-close" id="closeSupervisorModalBtn" aria-label="Close dialog">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form id="editSupervisorForm" class="admin-modal-form" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="admin-form-field">
                <label for="modal_supervisor_name">Full Name <span style="color: #b91c1c;">*</span></label>
                <input id="modal_supervisor_name" name="name" required minlength="2" maxlength="255">
            </div>

            <div class="admin-form-field">
                <label for="modal_supervisor_email">Email Address <span style="color: #b91c1c;">*</span></label>
                <input id="modal_supervisor_email" type="email" name="email" required maxlength="255">
            </div>

            <div class="admin-form-field">
                <label for="modal_supervisor_password">
                    New Password <span class="admin-field-hint">(leave blank to keep current)</span>
                </label>
                <input id="modal_supervisor_password" type="password" name="password" minlength="8" placeholder="Min. 8 characters" autocomplete="new-password">
            </div>

            <div class="admin-form-field">
                <label for="modal_supervisor_password_confirmation">Confirm New Password</label>
                <input id="modal_supervisor_password_confirmation" type="password" name="password_confirmation" minlength="8" placeholder="Retype only if changing password" autocomplete="new-password">
            </div>

            <div class="admin-modal-actions">
                <button type="button" class="admin-btn-secondary" id="cancelSupervisorModalBtn">Cancel</button>
                <button type="submit" class="admin-submit" style="width: auto; min-height: 2.4rem; padding: 0.6rem 1.25rem;">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editSupervisorModal');
    const form = document.getElementById('editSupervisorForm');
    const closeBtn = document.getElementById('closeSupervisorModalBtn');
    const cancelBtn = document.getElementById('cancelSupervisorModalBtn');
    const modalSubtitle = document.getElementById('modalSupervisorSubtitle');
    const nameInput = document.getElementById('modal_supervisor_name');
    const emailInput = document.getElementById('modal_supervisor_email');
    const passwordInput = document.getElementById('modal_supervisor_password');
    const passwordConfirmInput = document.getElementById('modal_supervisor_password_confirmation');

    function openModal(data) {
        form.action = data.action;
        nameInput.value = data.name || '';
        emailInput.value = data.email || '';
        passwordInput.value = '';
        passwordConfirmInput.value = '';

        if (modalSubtitle) {
            modalSubtitle.textContent = 'Editing ' + (data.name || 'supervisor');
        }

        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-edit-supervisor-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openModal({
                id: this.dataset.id,
                name: this.dataset.name,
                email: this.dataset.email,
                action: this.dataset.action
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
