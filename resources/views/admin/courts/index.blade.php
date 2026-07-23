@extends('layouts.admin')

@section('title', 'Court Management - ApexSports Hub')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Court Setup & Management</h2>
        <p class="text-secondary small mb-0">Manage courts per facility, rate overrides, capacity, and maintenance status.</p>
    </div>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary rounded-pill px-4 font-heading fw-bold mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#addCourtModal">
            <i class="fa-solid fa-plus me-1"></i> Add New Court
        </button>
    @endif
</div>

<!-- Filter Header -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form method="GET" action="{{ route('admin.courts.index') }}" class="row g-3 align-items-center">
        <div class="col-md-4">
            <select name="facility_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Facilities --</option>
                @foreach($facilities as $f)
                    <option value="{{ $f->id }}" {{ $facilityId == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>
        @if($facilityId)
            <div class="col-md-2">
                <a href="{{ route('admin.courts.index') }}" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
            </div>
        @endif
    </form>
</div>

<!-- Courts Datatable -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Court Name</th>
                    <th class="py-3">Facility</th>
                    <th class="py-3">Capacity</th>
                    <th class="py-3">Effective Hourly Rate</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 px-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courts as $court)
                    <tr>
                        <td class="py-3 px-4 fw-bold font-heading text-dark">
                            {{ $court->name }}
                        </td>
                        <td class="py-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                {{ $court->facility->name }}
                            </span>
                        </td>
                        <td class="py-3"><i class="fa-solid fa-users me-1 text-muted"></i> {{ $court->capacity }} Players</td>
                        <td class="py-3 fw-bold text-dark">
                            ${{ number_format($court->effective_rate, 2) }}/hr
                            @if($court->hourly_rate_override)
                                <span class="badge bg-warning-subtle text-warning small ms-1">Override</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($court->status === 'active')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span>
                            @elseif($court->status === 'maintenance')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">Maintenance</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            @if(auth()->user()->isAdmin())
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editCourtModal{{ $court->id }}" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.courts.destroy', $court->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete court {{ addslashes($court->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @else
                                <span class="small text-muted">View Only</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit Court Modal -->
                    <div class="modal fade" id="editCourtModal{{ $court->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.courts.update', $court->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title font-heading fw-bold">Edit Court: {{ $court->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Facility</label>
                                            <select name="facility_id" class="form-select" required>
                                                @foreach($facilities as $f)
                                                    <option value="{{ $f->id }}" {{ $court->facility_id == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Court Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $court->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Capacity (Players)</label>
                                            <input type="number" name="capacity" class="form-control" value="{{ $court->capacity }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Hourly Rate Override (Optional)</label>
                                            <input type="number" step="0.01" name="hourly_rate_override" class="form-control" value="{{ $court->hourly_rate_override }}" placeholder="Leave blank to use facility rate">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="active" {{ $court->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $court->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="maintenance" {{ $court->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary font-heading fw-bold">Update Court</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No courts configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $courts->withQueryString()->links() }}
</div>

<!-- Add Court Modal -->
<div class="modal fade" id="addCourtModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.courts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-heading fw-bold">Add New Court</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Facility</label>
                        <select name="facility_id" class="form-select" required>
                            <option value="">-- Select Facility --</option>
                            @foreach($facilities as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Court Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Court A (Mat 1)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Capacity (Players)</label>
                        <input type="number" name="capacity" class="form-control" value="4" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Hourly Rate Override (Optional)</label>
                        <input type="number" step="0.01" name="hourly_rate_override" class="form-control" placeholder="Leave blank to use facility default rate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-heading fw-bold">Add Court</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
