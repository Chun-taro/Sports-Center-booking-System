@extends('layouts.admin')

@section('title', 'User Access Management - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">User Management</h2>
        <p class="text-secondary small mb-0">Manage customer accounts, staff members, and system administrators.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 font-heading fw-bold mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus me-1"></i> Add New User Account
    </button>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="{{ $search }}">
        </div>
        <div class="col-md-4">
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Roles --</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="staff" {{ $role === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 font-heading fw-bold">Search</button>
            @if($search || $role)
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">User Name</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Phone</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 px-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="py-3 px-4 fw-bold font-heading text-dark">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold" style="width:34px; height:34px; font-size:0.8rem;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                {{ $u->name }}
                            </div>
                        </td>
                        <td class="py-3 text-secondary">{{ $u->email }}</td>
                        <td class="py-3 text-secondary">{{ $u->phone ?? 'N/A' }}</td>
                        <td class="py-3">
                            @if($u->role === 'admin')
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1 fw-bold">Administrator</span>
                            @elseif($u->role === 'staff')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1 fw-bold">Staff</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">Customer</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($u->is_active)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Deactivated</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit User">
                                <i class="fa-solid fa-user-pen"></i>
                            </button>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete account for {{ addslashes($u->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete User"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title font-heading fw-bold">Edit Account: {{ $u->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Full Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Email Address</label>
                                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $u->phone }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                                <option value="staff" {{ $u->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                                <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="active_{{ $u->id }}" value="1" {{ $u->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="active_{{ $u->id }}">Account Active</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary font-heading fw-bold">Update Account</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No user accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $users->withQueryString()->links() }}
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-heading fw-bold">Create New User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="John Smith">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required placeholder="+1 555 0192">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">User Role</label>
                        <select name="role" class="form-select" required>
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Initial Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-heading fw-bold">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
