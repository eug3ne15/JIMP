@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <section class="hr-intro">
        <div>
            <p class="admin-eyebrow">Overview</p>
            <h2>Manage users</h2>
            <p>View and manage user accounts, roles, and access.</p>
        </div>
    </section>

    <section class="hr-stats" aria-label="User statistics">
        <article class="hr-stat-card">
            <span class="hr-stat-label">Total users</span>
            <strong>{{ number_format($userCount) }}</strong>
        </article>
        <article class="hr-stat-card">
            <span class="hr-stat-label">Interns</span>
            <strong>{{ number_format($internCount) }}</strong>
            <a href="{{ route('admin.users.index', ['role' => 'intern']) }}">View interns</a>
        </article>
        <article class="hr-stat-card">
            <span class="hr-stat-label">Supervisors</span>
            <strong>{{ number_format($supervisorCount) }}</strong>
            <a href="{{ route('admin.users.index', ['role' => 'supervisor']) }}">View supervisors</a>
        </article>
        <article class="hr-stat-card">
            <span class="hr-stat-label">HR staff</span>
            <strong>{{ number_format($hrCount) }}</strong>
            <a href="{{ route('admin.users.index', ['role' => 'hr']) }}">View HR staff</a>
        </article>
    </section>

    <section class="admin-panel hr-shortcuts">
        <div class="admin-panel-heading">
            <div>
                <h2>Quick actions</h2>
                <p>Perform common tasks quickly.</p>
            </div>
        </div>
        <div class="hr-shortcut-grid">
            <a href="{{ route('admin.users.index') }}">Manage user accounts <span aria-hidden="true">→</span></a>
            <a href="{{ route('admin.users.create') }}">Add a new user <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
