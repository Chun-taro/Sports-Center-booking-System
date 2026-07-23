@extends('layouts.admin')

@section('title', 'Sports Facilities - ApexSports Hub')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold mb-1">Sports Facilities</h2>
        <p class="text-secondary small mb-0">Manage sports venues, rates, operating schedules, and player limits.</p>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary rounded-pill px-4 font-heading fw-bold">
            <i class="fa-solid fa-plus me-1"></i> Add New Facility
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="py-3 px-4">Facility Name</th>
                    <th class="py-3">Sport Type</th>
                    <th class="py-3">Hourly Rate</th>
                    <th class="py-3">Operating Hours</th>
                    <th class="py-3 text-center">Courts Count</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 px-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $f)
                    <tr>
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $f->image_url ?? 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=150&q=80' }}" class="rounded-3 object-fit-cover" style="width: 50px; height: 50px;" alt="{{ $f->name }}">
                                <div>
                                    <div class="fw-bold text-dark font-heading">{{ $f->name }}</div>
                                    <div class="small text-muted">Max {{ $f->max_players }} Players</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 text-uppercase" style="font-size:0.7rem;">
                                {{ str_replace('_', ' ', $f->sport_type) }}
                            </span>
                        </td>
                        <td class="py-3 fw-bold text-dark">${{ number_format($f->hourly_rate, 2) }}/hr</td>
                        <td class="py-3 small text-muted">
                            <i class="fa-regular fa-clock me-1 text-success"></i>
                            {{ date('g:i A', strtotime($f->open_time)) }} - {{ date('g:i A', strtotime($f->close_time)) }}
                        </td>
                        <td class="py-3 text-center fw-bold">{{ $f->courts_count }}</td>
                        <td class="py-3">
                            @if($f->is_active)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.facilities.edit', $f->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('admin.facilities.destroy', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this facility?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @else
                                <span class="small text-muted">View Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No facilities defined yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $facilities->links() }}
</div>
@endsection
