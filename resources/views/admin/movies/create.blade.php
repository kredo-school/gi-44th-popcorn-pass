@extends('layouts.admin')

@section('title', 'Add New Movie')
@section('page-title', 'Add New Movie')

@section('content')

    <div class="mb-3">
        <span class="text-secondary">Movies &gt; Add New</span>
    </div>

    <form action="{{ route('admin.movies.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-3">Movie Information</div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Movie Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter movie title..." value="{{ old('title') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small">Duration (minutes)</label>
                            <input type="number" name="duration" class="form-control" value="{{ old('duration', 120) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small">Genre</label>
                            <select name="genre_id" class="form-select" required>
                                <option value="">Select genre...</option>
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}" @selected(old('genre_id') == $genre->id)>{{ $genre->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small">Release Date</label>
                            <input type="date" name="released_date" class="form-control" value="{{ old('released_date') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small">Age Rating</label>
                            <select name="age_rating_id" class="form-select">
                                <option value="">Select rating...</option>
                                @foreach ($ageRatings as $rating)
                                    <option value="{{ $rating->id }}" @selected(old('age_rating_id') == $rating->id)>{{ $rating->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small">Movie Status</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="status" id="status_coming_soon" value="coming_soon" autocomplete="off" {{ old('status', 'coming_soon') == 'coming_soon' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning" for="status_coming_soon">Coming Soon</label>

                            <input type="radio" class="btn-check" name="status" id="status_now_showing" value="now_showing" autocomplete="off" {{ old('status') == 'now_showing' ? 'checked' : '' }}>
                            <label class="btn btn-outline-light" for="status_now_showing">Now Showing</label>

                            <input type="radio" class="btn-check" name="status" id="status_archived" value="archived" autocomplete="off" {{ old('status') == 'archived' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary" for="status_archived">Archived</label>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-secondary small">Synopsis</label>
                        <textarea name="synopsis" class="form-control" rows="3" placeholder="Enter movie synopsis...">{{ old('synopsis') }}</textarea>
                    </div>
                </div>

                <div class="card card-dark p-3 mt-3">
                    <div class="text-warning fw-bold mb-3">Movie Details</div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small">Director</label>
                            <input type="text" name="director" class="form-control" placeholder="Enter director name..." value="{{ old('director') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small">Cast Members</label>
                            <input type="text" name="cast" class="form-control" placeholder="Enter cast members..." value="{{ old('cast') }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-secondary small">Trailer URL</label>
                        <input type="url" name="trailer_url" class="form-control" placeholder="https://..." value="{{ old('trailer_url') }}">
                    </div>
                </div>

                <div class="card card-dark p-3 mt-3">
                    <div class="text-warning fw-bold mb-3">Movie Statistics</div>

                    <div class="row mb-0">
                        <div class="col-4">
                            <label class="form-label text-secondary small">Budget</label>
                            <input type="number" step="0.01" name="budget" class="form-control" placeholder="0" value="{{ old('budget') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-secondary small">Box Office Revenue</label>
                            <input type="number" step="0.01" name="box_office" class="form-control" placeholder="0" value="{{ old('box_office') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-secondary small">Popularity Score</label>
                            <input type="number" class="form-control" value="0" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-3">Media Upload</div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small">Poster Image</label>
                            <div class="border border-secondary rounded p-3 text-center text-secondary small">
                                Drag &amp; Drop or Click to Upload
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small">Banner Image</label>
                            <div class="border border-secondary rounded p-3 text-center text-secondary small">
                                Drag &amp; Drop or Click to Upload
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-dark p-3 mt-3">
                    <div class="text-warning fw-bold mb-3">Feature Settings</div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Featured Movie</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="card card-dark p-3 mt-3">
                    <div class="text-warning fw-bold mb-3">Actions</div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-warning">Publish Movie</button>
                        <button type="submit" class="btn btn-outline-light">Save Movie</button>
                        <a href="{{ route('admin.movies') }}" class="btn btn-outline-danger">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection