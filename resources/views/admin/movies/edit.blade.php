@extends('layouts.admin')


@section('title', 'Edit Movie')
@section('page-title', 'Movie Management')

@section('content')

    <div class="text-secondary small mb-3">Movies &gt; Edit</div>
    <h4 class="text-warning fw-bold mb-3">Edit Movie</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-movie-info" type="button">Movie
                Information</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-showtimes" type="button"
                id="showtimes-tab-btn">Showtimes</button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ===================== MOVIE INFORMATION TAB ===================== --}}
        <div class="tab-pane fade show active" id="tab-movie-info" role="tabpanel">
            <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card card-dark p-3 mb-3">
                            <div class="text-warning fw-bold mb-3">Movie Information</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Movie Title</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $movie->title) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Genre</label>
                                    <div class="row">
                                        @foreach ($genres as $genre)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input genre-checkbox" type="checkbox"
                                                        name="genre_ids[]" value="{{ $genre->id }}"
                                                        id="genre{{ $genre->id }}"
                                                        {{ in_array($genre->id, old('genre_ids', $movie->genres->pluck('id')->toArray())) ? 'checked' : '' }}>

                                                    <label class="form-check-label" for="genre{{ $genre->id }}">
                                                        {{ $genre->title }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Duration (minutes)</label>
                                    <input type="number" name="duration" class="form-control"
                                        value="{{ old('duration', $movie->duration) }}" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Age Rating</label>
                                    <select name="age_rating_id" class="form-select">
                                        <option value="">Select rating...</option>
                                        @foreach ($ageRatings as $ageRating)
                                            <option value="{{ $ageRating->id }}"
                                                {{ old('age_rating_id', $movie->age_rating_id) == $ageRating->id ? 'selected' : '' }}>
                                                {{ $ageRating->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Release Date</label>
                                    <input type="date" name="released_date" class="form-control"
                                        value="{{ old('released_date', optional($movie->released_date)->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ old('end_date', optional($movie->end_date)->format('Y-m-d')) }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-secondary small">
                                        Movie Status
                                    </label>

                                    <div>
                                        @if ($movie->status === 'coming_soon')
                                            <span class="bg-warning text-dark p-2">
                                                Coming Soon
                                            </span>
                                        @elseif($movie->status === 'now_showing')
                                            <span class="bg-success p-2">
                                                Now Showing
                                            </span>
                                        @elseif($movie->status === 'archived')
                                            <span class="bg-secondary p-2">
                                                Archived
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-secondary small">Synopsis</label>
                                    <textarea name="synopsis" class="form-control" rows="3">{{ old('synopsis', $movie->synopsis) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card card-dark p-3 mb-3">
                            <div class="text-warning fw-bold mb-3">Movie Details</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Director</label>
                                    <input type="text" name="director" class="form-control"
                                        value="{{ old('director', $movie->director) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Cast Members (Max 6)</label>
                                    @php
                                        $casts = old(
                                            'cast',
                                            is_array($movie->cast)
                                                ? $movie->cast
                                                : json_decode($movie->cast, true) ?? [],
                                        );
                                    @endphp

                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text" name="cast[]" class="form-control mb-2"
                                            placeholder="Cast Member {{ $i + 1 }}" value="{{ $casts[$i] ?? '' }}">
                                    @endfor
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Trailer URL</label>
                                    <input type="url" name="trailer_url" class="form-control"
                                        value="{{ old('trailer_url', $movie->trailer_url) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Search Keywords</label>
                                    <input type="text" name="search_keywords" class="form-control"
                                        value="{{ old('search_keywords', $movie->search_keywords) }}">
                                </div>
                            </div>
                        </div>

                        <div class="card card-dark p-3">
                            <div class="text-warning fw-bold mb-3">Movie Statistics</div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Budget</label>
                                    <input type="number" step="0.01" name="budget" class="form-control"
                                        value="{{ old('budget', $movie->budget) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Box Office Revenue</label>
                                    <input type="number" step="0.01" name="box_office" class="form-control"
                                        value="{{ old('box_office', $movie->box_office) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Popularity Score</label>
                                    <input type="number" class="form-control" value="{{ $movie->popularity_score }}"
                                        disabled>
                                    <small class="text-secondary">Calculated automatically</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-dark p-3 mb-3">
                            <div class="text-warning fw-bold mb-3">Media</div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-secondary small">Poster Image URL</label>
                                    <input type="url" name="poster_url" class="form-control"
                                        value="{{ old('poster_url', $movie->poster_url) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-secondary small">Banner Image URL</label>
                                    <input type="url" name="banner_image_url" class="form-control"
                                        value="{{ old('banner_image_url', $movie->banner_image_url) }}">
                                </div>
                            </div>
                        </div>

                        <div class="card card-dark p-3 mb-3">
                            <div class="text-warning fw-bold mb-3">Feature Settings</div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                    {{ old('is_featured', $movie->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured Movie</label>
                            </div>

                            <div class="mb-0">
                                <label class="form-label text-secondary small">Display Priority</label>
                                <select name="priority_order" class="form-select">
                                    <option value="1"
                                        {{ old('priority_order', $movie->priority_order) == 1 ? 'selected' : '' }}>High
                                    </option>
                                    <option value="999"
                                        {{ old('priority_order', $movie->priority_order) == 999 ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="9999"
                                        {{ old('priority_order', $movie->priority_order) == 9999 ? 'selected' : '' }}>Low
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="card card-dark p-3">
                            <div class="text-warning fw-bold mb-3">Actions</div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">Update Movie</button>
                                <a href="{{ route('admin.movies') }}" class="btn btn-outline-light">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===================== SHOWTIMES TAB ===================== --}}
        <div class="tab-pane fade" id="tab-showtimes" role="tabpanel">

            {{-- Generate Form --}}
            <div class="card card-dark p-3 mb-3">
                <div class="text-warning fw-bold mb-3">Generate Showtimes</div>
                <div class="text-secondary small mb-3">
                    Set a recurrence pattern and click Generate to create multiple showtimes at once.
                </div>

                <div class="row g-3" id="generate-form">
                    {{-- Cinema --}}
                    <div class="col-md-3">
                        <label class="form-label text-secondary small">
                            Cinema
                        </label>

                        <select class="form-select" id="gen-cinema">
                            <option value="">Select cinema...</option>

                            @foreach ($cinemas as $cinema)
                                <option value="{{ $cinema->id }}">
                                    {{ $cinema->cinema_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Screen --}}
                    <div class="col-md-3">
                        <label class="form-label text-secondary small">
                            Screen
                        </label>

                        <select class="form-select" id="gen-screen" disabled>
                            <option value="">Select screen...</option>

                            @foreach ($screens as $screen)
                                <option value="{{ $screen->id }}" data-cinema="{{ $screen->cinema_id }}" hidden>
                                    Screen {{ $screen->screen_number }}
                                    - {{ $screen->screen_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-secondary small">Days of Week</label>
                        <div class="d-flex gap-3 flex-wrap">
                            @foreach (['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $label => $value)
                                <div class="form-check">
                                    <input class="form-check-input gen-day" type="checkbox" value="{{ $value }}"
                                        id="day-{{ $value }}" checked>
                                    <label class="form-check-label"
                                        for="day-{{ $value }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-secondary small">Time Slots (up to 6)</label>
                        <div class="row g-2">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="col-md-2">
                                    <input type="time" class="form-control gen-slot" placeholder="--:--">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn btn-warning" id="generate-btn"
                            data-url="{{ route('admin.movies.showtimes.generate', $movie->id) }}">
                            Generate Showtimes
                        </button>
                        <span class="ms-3 text-secondary small" id="generate-msg"></span>
                    </div>
                </div>
            </div>

            {{-- Showtime List --}}
            <div class="card card-dark p-3">

                <div class="text-warning fw-bold mb-3">
                    Existing Showtimes
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif


                <div id="showtime-list-container" class="showtime-scroll">

                    @if ($showtimes->isEmpty())

                        <div class="text-secondary text-center py-3">
                            No showtimes registered.
                        </div>
                    @else
                        @foreach ($showtimes->groupBy(function ($showtime) {
            return $showtime->start_time->format('Y/m/d');
        }) as $date => $dailyShowtimes)
                            <div class="d-flex align-items-center mt-3 mb-2">
                                <i class="fa-solid fa-calendar-days text-warning me-2"></i>
                                <h6 class="mb-0 text-warning">
                                    {{ $date }}
                                </h6>
                            </div>


                            <table class="table table-dark table-hover align-middle mb-4">

                                <thead>
                                    <tr>
                                        <th width="25%">Time</th>
                                        <th width="30%">Cinema</th>
                                        <th width="25%">Screen</th>
                                        <th width="20%" class="text-end">Action</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach ($dailyShowtimes as $showtime)
                                        <tr>
                                            <td>
                                                {{ $showtime->start_time->format('H:i') }}
                                                -
                                                {{ $showtime->end_time->format('H:i') }}
                                            </td>

                                            <td>
                                                {{ $showtime->screen->cinema->cinema_name ?? '—' }}
                                            </td>

                                            <td>
                                                Screen {{ $showtime->screen->screen_number ?? '—' }}
                                                -
                                                {{ $showtime->screen->screen_type ?? '—' }}
                                            </td>

                                            <td class="text-end">
                                                <form action="{{ route('admin.showtimes.delete', $showtime->id) }}"
                                                    method="POST" onsubmit="return confirm('Delete this showtime?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        @endforeach

                    @endif

                </div>


            </div>
        </div>

    </div>

@endsection
