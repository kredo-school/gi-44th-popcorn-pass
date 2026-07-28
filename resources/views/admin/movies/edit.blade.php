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
                                    <select name="genre_id" class="form-select" required>
                                        <option value="">Select genre...</option>
                                        @foreach ($genres as $genre)
                                            <option value="{{ $genre->id }}"
                                                {{ old('genre_id', $movie->genre_id) == $genre->id ? 'selected' : '' }}>
                                                {{ $genre->title }}</option>
                                        @endforeach
                                    </select>
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
                                    <label class="form-label text-secondary small">Movie Status</label><br>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="status" id="status-coming-soon"
                                            value="coming_soon" autocomplete="off"
                                            {{ old('status', $movie->status) == 'coming_soon' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning" for="status-coming-soon">Coming Soon</label>

                                        <input type="radio" class="btn-check" name="status" id="status-now-showing"
                                            value="now_showing" autocomplete="off"
                                            {{ old('status', $movie->status) == 'now_showing' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success" for="status-now-showing">Now Showing</label>

                                        <input type="radio" class="btn-check" name="status" id="status-archived"
                                            value="archived" autocomplete="off"
                                            {{ old('status', $movie->status) == 'archived' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="status-archived">Archived</label>
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
                                    <label class="form-label text-secondary small">Cast Members</label>
                                    <input type="text" name="cast" class="form-control"
                                        value="{{ old('cast', $movie->cast) }}">
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
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Screen</label>
                        <select class="form-select" id="gen-screen">
                            <option value="">Select screen...</option>
                            @foreach ($screens as $screen)
                                <option value="{{ $screen->id }}">{{ $screen->cinema->cinema_name }} - Screen
                                    {{ $screen->screen_number }} - {{ $screen->screen_type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">Start Date</label>
                        <input type="date" class="form-control" id="gen-start-date">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small">End Date</label>
                        <input type="date" class="form-control" id="gen-end-date">
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
                        <button type="button" class="btn btn-warning" id="generate-btn">Generate Showtimes</button>
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
                                        <th width="30%">Time</th>
                                        <th width="40%">Screen</th>
                                        <th width="30%" class="text-end">Action</th>
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
                                                Screen {{ $showtime->screen->screen_number }}
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const generateUrl = "{{ route('admin.movies.showtimes.generate', $movie->id) }}";
            const csrfToken = "{{ csrf_token() }}";

            const generateBtn = document.querySelector('#generate-btn');

            generateBtn?.addEventListener('click', function() {

                const screenId = document.querySelector('#gen-screen').value;
                const startDate = document.querySelector('#gen-start-date').value;
                const endDate = document.querySelector('#gen-end-date').value;

                const days = [...document.querySelectorAll('.gen-day:checked')]
                    .map(el => parseInt(el.value));

                const timeSlots = [...document.querySelectorAll('.gen-slot')]
                    .map(el => el.value)
                    .filter(Boolean);

                const msgEl = document.querySelector('#generate-msg');

                if (!screenId || !startDate || !endDate || days.length === 0) {
                    msgEl.textContent =
                        'Please fill in Screen, Start Date, End Date, and at least one day.';
                    msgEl.className = 'ms-3 text-danger small';
                    return;
                }

                if (timeSlots.length === 0) {
                    msgEl.textContent = 'Please enter at least one time slot.';
                    msgEl.className = 'ms-3 text-danger small';
                    return;
                }

                msgEl.textContent = 'Generating...';
                msgEl.className = 'ms-3 text-secondary small';

                const body = new FormData();

                body.append('_token', csrfToken);
                body.append('screen_id', screenId);
                body.append('start_date', startDate);
                body.append('end_date', endDate);

                days.forEach(day => body.append('days[]', day));
                timeSlots.forEach(slot => body.append('time_slots[]', slot));

                fetch(generateUrl, {
                        method: 'POST',
                        body: body
                    })
                    .then(async response => {

                        console.log('Status:', response.status);

                        const text = await response.text();

                        console.log(text);

                        return JSON.parse(text);

                    })
                    .then(data => {

                        console.log(data);

                        if (data.success) {

                            msgEl.textContent = data.message;
                            msgEl.className = 'ms-3 text-success small';

                            setTimeout(() => {
                                location.reload();
                            }, 1000);

                        } else {

                            msgEl.textContent = data.message ?? 'Failed to generate showtimes.';
                            msgEl.className = 'ms-3 text-danger small';

                        }

                    })
                    .catch(error => {

                        console.error(error);

                        msgEl.textContent = error.message;
                        msgEl.className = 'ms-3 text-danger small';

                    });

            });

        });
    </script>
@endsection
