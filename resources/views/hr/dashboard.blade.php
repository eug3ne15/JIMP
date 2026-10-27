@extends('hr.layout')

@section('title', 'HR Dashboard')
@section('heading', 'HR Dashboard')

@section('content')
    <section class="hr-intro">
        <div>
            <p class="admin-eyebrow">Overview</p>
            <h2>Intern Applications</h2>
            <p>Review intern applications and manage records.</p>
        </div>
    </section>

    <section class="hr-stats" aria-label="Programme statistics">
        <article class="hr-stat-card">
            <span class="hr-stat-label">Total applications</span>
            <strong>{{ number_format($applicationCount) }}</strong>
        </article>
        <article class="hr-stat-card">
            <span class="hr-stat-label">Supervisors</span>
            <strong>{{ number_format($supervisorCount) }}</strong>
            <a href="{{ route('hr.supervisors.index') }}">Manage supervisors</a>
        </article>
        <article class="hr-stat-card">
            <span class="hr-stat-label">Open Roles</span>
            <strong>{{ number_format($courseCount) }}</strong>
            <a href="{{ route('hr.courses.index') }}">Manage open roles</a>
        </article>
    </section>

    <section class="admin-panel hr-shortcuts">
        <div class="admin-panel-heading">
            <div>
                <h2>Manage records</h2>
                <p>Keep records up to date.</p>
            </div>
        </div>
        <div class="hr-shortcut-grid">
            <a href="{{ route('hr.courses.create') }}">Add an open role <span aria-hidden="true">→</span></a>
            <a href="{{ route('hr.supervisors.create') }}">Add supervisor <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
