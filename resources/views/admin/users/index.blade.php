@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')

    <form method="GET" action="{{ route('admin.users') }}" class="d-flex gap-2 mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search users..." style="max-width: 250px;" value="{{ request('search') }}">

        <select name="role" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('role', 'all') == 'all' ? 'selected' : '' }}>Role: All</option>
            @foreach ($roleOptions as $value => $label)
                <option value="{{ $value }}" {{ request('role') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="status" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Status: All</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-outline-warning">Search</button>
        @if (request()->filled('search') || request('role', 'all') !== 'all' || request('status', 'all') !== 'all')
            <a href="{{ route('admin.users') }}" class="btn btn-outline-light">
                Reset
            </a>
        @endif
    </form>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card card-dark p-3">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="user-row" data-user-id="{{ $user->id }}" style="cursor: pointer;">
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $roleOptions[$user->role] ?? 'Unknown' }}</span>
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—' }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-3">User Details</div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Username</label>
                    <div class="form-control bg-transparent text-white" id="detail-username">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Email</label>
                    <div class="form-control bg-transparent text-white" id="detail-email">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Phone</label>
                    <div class="form-control bg-transparent text-white" id="detail-phone">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Date of Birth</label>
                    <div class="form-control bg-transparent text-white" id="detail-dob">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Last Login</label>
                    <div class="form-control bg-transparent text-white" id="detail-last-login">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Joined</label>
                    <div class="form-control bg-transparent text-white" id="detail-created">—</div>
                </div>

                <hr class="border-secondary">

                <form id="user-update-form" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Role</label>
                        <select name="role" id="detail-role" class="form-select">
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="detail-is-active">
                        <label class="form-check-label" for="detail-is-active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-warning" id="user-save-btn" disabled>Save Changes</button>
                </form>
            </div>
        </div>
    </div>

@endsection
