@extends('layouts.admin')

@section('title', 'Edit Facility - ' . $facility->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.facilities.index') }}" class="text-secondary text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities</a>
    <h2 class="font-heading fw-bold mt-2">Edit Sports Facility</h2>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
    <form action="{{ route('admin.facilities.update', $facility->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-bold small text-secondary">Facility Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $facility->name) }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="sport_type" class="form-label fw-bold small text-secondary">Sport Type Category</label>
                <select name="sport_type" id="sport_type" class="form-select @error('sport_type') is-invalid @enderror" required>
                    <option value="badminton" {{ old('sport_type', $facility->sport_type) == 'badminton' ? 'selected' : '' }}>Badminton</option>
                    <option value="basketball" {{ old('sport_type', $facility->sport_type) == 'basketball' ? 'selected' : '' }}>Basketball</option>
                    <option value="pickleball" {{ old('sport_type', $facility->sport_type) == 'pickleball' ? 'selected' : '' }}>Pickleball</option>
                    <option value="volleyball" {{ old('sport_type', $facility->sport_type) == 'volleyball' ? 'selected' : '' }}>Volleyball</option>
                    <option value="tennis" {{ old('sport_type', $facility->sport_type) == 'tennis' ? 'selected' : '' }}>Tennis</option>
                    <option value="table_tennis" {{ old('sport_type', $facility->sport_type) == 'table_tennis' ? 'selected' : '' }}>Table Tennis</option>
                    <option value="futsal" {{ old('sport_type', $facility->sport_type) == 'futsal' ? 'selected' : '' }}>Futsal</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="hourly_rate" class="form-label fw-bold small text-secondary">Base Hourly Rate ($)</label>
                <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" value="{{ old('hourly_rate', $facility->hourly_rate) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="max_players" class="form-label fw-bold small text-secondary">Max Players per Court</label>
                <input type="number" name="max_players" id="max_players" class="form-control @error('max_players') is-invalid @enderror" value="{{ old('max_players', $facility->max_players) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="open_time" class="form-label fw-bold small text-secondary">Opening Time</label>
                <input type="time" name="open_time" id="open_time" class="form-control" value="{{ old('open_time', $facility->open_time) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="close_time" class="form-label fw-bold small text-secondary">Closing Time</label>
                <input type="time" name="close_time" id="close_time" class="form-control" value="{{ old('close_time', $facility->close_time) }}" required>
            </div>

            <div class="col-12 mb-3">
                <label for="image_url" class="form-label fw-bold small text-secondary">Image URL</label>
                <input type="url" name="image_url" id="image_url" class="form-control" value="{{ old('image_url', $facility->image_url) }}">
            </div>

            <div class="col-12 mb-3">
                <label for="description" class="form-label fw-bold small text-secondary">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $facility->description) }}</textarea>
            </div>

            <div class="col-12 mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $facility->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Facility Active for Reservations</label>
                </div>
            </div>
        </div>

        <div class="text-end border-top pt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-5 font-heading fw-bold">Update Facility</button>
        </div>
    </form>
</div>
@endsection
