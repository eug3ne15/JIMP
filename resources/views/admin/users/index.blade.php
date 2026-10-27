@extends('admin.layout')

@section('title', 'User Management')
@section('heading', 'User Management')

@section('content')
<section class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Users</h2>
            <p>Manage user details, passwords, and access roles.</p>
        </div>
        <span class="admin-count" id="adminUserCount">{{ $users->total() }} users</span>
    </div>

    <!-- Role Filter Tabs -->
    <div class="admin-role-tabs-wrap">
        <div class="admin-role-tabs" role="tablist" aria-label="Filter users by role">
            <button type="button"
                    class="admin-role-tab {{ ($selectedRole ?? 'all') === 'all' ? 'active' : '' }}"
                    data-role="all"
                    role="tab"
                    aria-selected="{{ ($selectedRole ?? 'all') === 'all' ? 'true' : 'false' }}">
                <span class="tab-label">All</span>
                <span class="admin-tab-badge">{{ $roleCounts['all'] ?? $users->total() }}</span>
            </button>
            <button type="button"
                    class="admin-role-tab {{ ($selectedRole ?? 'all') === 'intern' ? 'active' : '' }}"
                    data-role="intern"
                    role="tab"
                    aria-selected="{{ ($selectedRole ?? 'all') === 'intern' ? 'true' : 'false' }}">
                <span class="tab-label">Interns</span>
                <span class="admin-tab-badge">{{ $roleCounts['intern'] ?? 0 }}</span>
            </button>
            <button type="button"
                    class="admin-role-tab {{ ($selectedRole ?? 'all') === 'hr' ? 'active' : '' }}"
                    data-role="hr"
                    role="tab"
                    aria-selected="{{ ($selectedRole ?? 'all') === 'hr' ? 'true' : 'false' }}">
                <span class="tab-label">HR's</span>
                <span class="admin-tab-badge">{{ $roleCounts['hr'] ?? 0 }}</span>
            </button>
            <button type="button"
                    class="admin-role-tab {{ ($selectedRole ?? 'all') === 'supervisor' ? 'active' : '' }}"
                    data-role="supervisor"
                    role="tab"
                    aria-selected="{{ ($selectedRole ?? 'all') === 'supervisor' ? 'true' : 'false' }}">
                <span class="tab-label">Supervisors</span>
                <span class="admin-tab-badge">{{ $roleCounts['supervisor'] ?? 0 }}</span>
            </button>
            <button type="button"
                    class="admin-role-tab {{ ($selectedRole ?? 'all') === 'admin' ? 'active' : '' }}"
                    data-role="admin"
                    role="tab"
                    aria-selected="{{ ($selectedRole ?? 'all') === 'admin' ? 'true' : 'false' }}">
                <span class="tab-label">Admin</span>
                <span class="admin-tab-badge">{{ $roleCounts['admin'] ?? 0 }}</span>
            </button>
        </div>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr class="user-row" data-role="{{ $user->role }}">
                    <td data-label="Name">
                        <strong>{{ $user->name }}</strong>
                        @if ($user->role === 'intern' && ($user->school || $user->course))
                            <small class="user-subtext">{{ $user->school ?? '' }}{{ ($user->school && $user->course) ? ' — ' : '' }}{{ $user->course ?? '' }}</small>
                        @endif
                    </td>
                    <td data-label="Email">{{ $user->email }}</td>
                    <td data-label="Role"><span class="admin-role admin-role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td data-label="Actions">
                        <div class="admin-actions">
                            <button type="button"
                                    class="admin-edit-link js-edit-user-btn"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->role }}"
                                    data-school="{{ $user->school ?? '' }}"
                                    data-course="{{ $user->course ?? '' }}"
                                    data-action="{{ route('admin.users.update', $user) }}">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="admin-delete-button" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="admin-empty">No users have been created yet.</td>
                </tr>
            @endforelse
                <tr id="tabEmptyRow" style="display: none;">
                    <td colspan="4" class="admin-empty" id="tabEmptyText">No users found for this role.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="admin-pagination">{{ $users->links() }}</div>
</section>

<!-- Edit User Modal Popup -->
<div id="editUserModal" class="admin-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="modalTitle">
    <div class="admin-modal-dialog">
        <div class="admin-modal-header">
            <div>
                <h3 id="modalTitle">Edit User</h3>
                <p id="modalSubtitle">Update account details and access role</p>
            </div>
            <button type="button" class="admin-modal-close" id="closeEditModalBtn" aria-label="Close modal">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form id="editUserForm" class="admin-modal-form" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="admin-form-field">
                <label for="modal_name">Full name</label>
                <input id="modal_name" name="name" required>
            </div>

            <div class="admin-form-field">
                <label for="modal_email">Email address</label>
                <input id="modal_email" type="email" name="email" required>
            </div>

            <div class="admin-form-field">
                <label for="modal_role">Role</label>
                <select id="modal_role" name="role" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-field" id="modal_university_group">
                <label for="modal_school">University / Institution</label>
                <input id="modal_school" name="school" placeholder="e.g. Egerton University">
            </div>

            <div class="admin-form-field" id="modal_course_group">
                <label for="modal_course">Course of study</label>
                <input id="modal_course" name="course" placeholder="e.g. Bachelor of Laws (LL.B)">
            </div>

            <div class="admin-form-field">
                <label for="modal_password">New password <span class="admin-field-hint">(leave blank to keep unchanged)</span></label>
                <input id="modal_password" type="password" name="password" placeholder="Leave blank to keep unchanged">
            </div>

            <div class="admin-form-field">
                <label for="modal_password_confirmation">Confirm new password</label>
                <input id="modal_password_confirmation" type="password" name="password_confirmation" placeholder="Confirm only if changing password">
            </div>

            <div class="admin-modal-actions">
                <button type="button" class="admin-btn-secondary" id="cancelEditModalBtn">Cancel</button>
                <button type="submit" class="admin-submit" style="width: auto; min-height: 2.4rem; padding: 0.6rem 1.25rem;">Save changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editUserModal');
    const form = document.getElementById('editUserForm');
    const closeBtn = document.getElementById('closeEditModalBtn');
    const cancelBtn = document.getElementById('cancelEditModalBtn');
    const modalSubtitle = document.getElementById('modalSubtitle');

    const nameInput = document.getElementById('modal_name');
    const emailInput = document.getElementById('modal_email');
    const roleSelect = document.getElementById('modal_role');
    const schoolInput = document.getElementById('modal_school');
    const courseInput = document.getElementById('modal_course');
    const passwordInput = document.getElementById('modal_password');
    const passwordConfirmInput = document.getElementById('modal_password_confirmation');

    const uniGroup = document.getElementById('modal_university_group');
    const courseGroup = document.getElementById('modal_course_group');

    function toggleInternFields() {
        const isIntern = roleSelect.value === 'intern';
        if (uniGroup) {
            uniGroup.style.display = isIntern ? 'flex' : 'none';
            if (schoolInput) schoolInput.disabled = !isIntern;
        }
        if (courseGroup) {
            courseGroup.style.display = isIntern ? 'flex' : 'none';
            if (courseInput) courseInput.disabled = !isIntern;
        }
    }

    roleSelect.addEventListener('change', toggleInternFields);

    function openModal(data) {
        form.action = data.action;
        nameInput.value = data.name || '';
        emailInput.value = data.email || '';
        roleSelect.value = data.role || 'intern';
        schoolInput.value = data.school || '';
        courseInput.value = data.course || '';
        passwordInput.value = '';
        passwordConfirmInput.value = '';

        if (modalSubtitle) {
            modalSubtitle.textContent = 'Editing ' + (data.name || 'user account');
        }

        toggleInternFields();

        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => nameInput.focus(), 50);
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openModal({
                id: this.dataset.id,
                name: this.dataset.name,
                email: this.dataset.email,
                role: this.dataset.role,
                school: this.dataset.school,
                course: this.dataset.course,
                action: this.dataset.action
            });
        });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Close on backdrop click outside the dialog
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });

    // Role filter tabs handling
    const roleTabs = document.querySelectorAll('.admin-role-tab');
    const userRows = document.querySelectorAll('.user-row');
    const tabEmptyRow = document.getElementById('tabEmptyRow');
    const tabEmptyText = document.getElementById('tabEmptyText');
    const adminUserCount = document.getElementById('adminUserCount');

    const roleNameLabels = {
        all: 'users',
        intern: 'Interns',
        hr: "HR's",
        supervisor: 'Supervisors',
        admin: 'Admin'
    };

    function filterUsersByRole(role) {
        let visibleCount = 0;

        userRows.forEach(row => {
            const rowRole = row.getAttribute('data-role');
            if (role === 'all' || rowRole === role) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (tabEmptyRow && tabEmptyText) {
            if (visibleCount === 0) {
                tabEmptyText.textContent = 'No ' + (roleNameLabels[role] || role) + ' found in the system.';
                tabEmptyRow.style.display = '';
            } else {
                tabEmptyRow.style.display = 'none';
            }
        }

        if (adminUserCount) {
            if (role === 'all') {
                adminUserCount.textContent = visibleCount + ' users';
            } else {
                adminUserCount.textContent = 'Showing ' + visibleCount + ' ' + (roleNameLabels[role] || role);
            }
        }

        roleTabs.forEach(tab => {
            const isSelected = tab.getAttribute('data-role') === role;
            tab.classList.toggle('active', isSelected);
            tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        });

        // Update URL query parameter without full reload
        const url = new URL(window.location);
        if (role === 'all') {
            url.searchParams.delete('role');
        } else {
            url.searchParams.set('role', role);
        }
        window.history.replaceState({}, '', url);
    }

    roleTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const role = this.getAttribute('data-role');
            filterUsersByRole(role);
        });
    });

    // Check if initial role is set (from URL query e.g. ?role=intern or server)
    const initialUrlRole = new URLSearchParams(window.location.search).get('role') || '{{ $selectedRole ?? "all" }}';
    if (initialUrlRole && initialUrlRole !== 'all' && roleNameLabels[initialUrlRole]) {
        filterUsersByRole(initialUrlRole);
    }
});
</script>
@endsection
