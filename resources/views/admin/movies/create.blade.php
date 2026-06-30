@extends('layouts.admin')

@section('title', 'Add New Movie')
@section('page-title', 'Movie Management')

@section('content')


    <div class="text-secondary small mb-3">Movies &gt; Add New</div>
    <h4 class="text-warning fw-bold mb-3">Add New Movie</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.movies.store') }}">
        @csrf
        @if ($errors->any())
            <div style="color:red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card card-dark p-3 mb-3">
                    <div class="text-warning fw-bold mb-3">Movie Information</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Movie Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter movie title..."
                                value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Genre</label>
                            <select name="genre_id" class="form-select" required>
                                <option value="">Select genre...</option>
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}"
                                        {{ old('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Duration (minutes)</label>
                            <input type="number" name="duration" class="form-control" value="{{ old('duration', 120) }}"
                                min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Age Rating</label>
                            <select name="age_rating_id" class="form-select">
                                <option value="">Select rating...</option>
                                @foreach ($ageRatings as $ageRating)
                                    <option value="{{ $ageRating->id }}"
                                        {{ old('age_rating_id') == $ageRating->id ? 'selected' : '' }}>
                                        {{ $ageRating->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Release Date</label>
                            <input type="date" name="released_date" class="form-control"
                                value="{{ old('released_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-secondary small">Movie Status</label><br>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="status" id="status-coming-soon"
                                    value="coming_soon" autocomplete="off"
                                    {{ old('status', 'coming_soon') == 'coming_soon' ? 'checked' : '' }}>
                                <label class="btn btn-outline-warning" for="status-coming-soon">Coming Soon</label>

                                <input type="radio" class="btn-check" name="status" id="status-now-showing"
                                    value="now_showing" autocomplete="off"
                                    {{ old('status') == 'now_showing' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="status-now-showing">Now Showing</label>

                                <input type="radio" class="btn-check" name="status" id="status-archived" value="archived"
                                    autocomplete="off" {{ old('status') == 'archived' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary" for="status-archived">Archived</label>
                            </div>
                            <div class="form-text text-secondary">
                                Note: once a Release Date / End Date is set, this status will be updated automatically over
                                time.
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-secondary small">Synopsis</label>
                            <textarea name="synopsis" class="form-control" rows="3" placeholder="Enter movie synopsis...">{{ old('synopsis') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-dark p-3 mb-3">
                    <div class="text-warning fw-bold mb-3">Movie Details</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Director</label>
                            <input type="text" name="director" class="form-control" placeholder="Enter director name..."
                                value="{{ old('director') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Cast Members</label>
                            <input type="text" name="cast" class="form-control" placeholder="Enter cast members..."
                                value="{{ old('cast') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Trailer URL</label>
                            <input type="url" name="trailer_url" class="form-control" placeholder="https://..."
                                value="{{ old('trailer_url') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Search Keywords</label>
                            <input type="text" name="search_keywords" class="form-control"
                                placeholder="action, thriller..." value="{{ old('search_keywords') }}">
                        </div>
                    </div>
                </div>

                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-3">Movie Statistics</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Budget</label>
                            <input type="number" step="0.01" name="budget" class="form-control"
                                value="{{ old('budget', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Box Office Revenue</label>
                            <input type="number" step="0.01" name="box_office" class="form-control"
                                value="{{ old('box_office', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Popularity Score</label>
                            <input type="number" class="form-control" value="0" disabled>
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
                            <input type="url" name="poster_url" class="form-control" placeholder="https://..."
                                value="{{ old('poster_url') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary small">Banner Image URL</label>
                            <input type="url" name="banner_image_url" class="form-control" placeholder="https://..."
                                value="{{ old('banner_image_url') }}">
                        </div>
                    </div>
                </div>

                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-3">Feature Settings</div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                            {{ old('is_featured') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">Featured Movie</label>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-secondary small">Display Priority</label>
                        <select name="priority_order" class="form-select">
                            <option value="1" {{ old('priority_order') == 1 ? 'selected' : '' }}>High</option>
                            <option value="999" {{ old('priority_order', 999) == 999 ? 'selected' : '' }}>Normal
                            </option>
                            <option value="9999" {{ old('priority_order') == 9999 ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-1">Showtimes</div>
                    <div class="text-secondary small mb-3">
                        Optional — add up to 6 showtimes for this movie. Leave a row completely blank to skip it.
                        The end time is calculated automatically from the movie's duration.
                    </div>

                    @for ($i = 0; $i < 6; $i++)
                        @php
                            $oldDate = old('showtimes.' . $i . '.date');
                            $oldStartTime = old('showtimes.' . $i . '.start_time');
                            $oldCinemaId = old('showtimes.' . $i . '.cinema_id');
                            $oldScreenId = old('showtimes.' . $i . '.screen_id');
                        @endphp
                        <div class="row g-2 mb-2 align-items-center">
                            <div class="col-md-1 text-secondary small text-center">#{{ $i + 1 }}</div>
                            <div class="col-md-3">
                                <select name="showtimes[{{ $i }}][cinema_id]"
                                    class="form-select showtime-cinema" data-index="{{ $i }}">
                                    <option value="">Cinema...</option>
                                    @foreach ($cinemas as $cinema)
                                        <option value="{{ $cinema->id }}"
                                            {{ old('showtimes.' . $i . '.cinema_id') == $cinema->id ? 'selected' : '' }}>
                                            {{ $cinema->cinema_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="showtimes[{{ $i }}][screen_id]"
                                    class="form-select showtime-screen" data-index="{{ $i }}">
                                    <option value="">Screen...</option>
                                    @foreach ($screens as $screen)
                                        <option value="{{ $screen->id }}" data-cinema="{{ $screen->cinema_id }}"
                                            {{ old('showtimes.' . $i . '.screen_id') == $screen->id ? 'selected' : '' }}>
                                            {{ $screen->cinema->cinema_name }} - Screen {{ $screen->screen_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="showtimes[{{ $i }}][date]" class="form-control"
                                    value="{{ $oldDate }}">
                            </div>
                            <div class="col-md-2">
                                <input type="time" name="showtimes[{{ $i }}][start_time]"
                                    class="form-control" value="{{ $oldStartTime }}">
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card card-dark p-3">
                    <div class="text-warning fw-bold mb-3">Actions</div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Save Movie</button>
                        <a href="{{ route('admin.movies') }}" class="btn btn-outline-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section('styles')
    <style>
        select.form-select {
            display: block !important;
            background-color: white !important;
            color: black !important;
        }
    </style>
@endsection
@section('scripts')
    <script>
        function filterScreensForRow(cinemaSelect) {
            const index = cinemaSelect.dataset.index;
            const screenSelect = document.querySelector(`.showtime-screen[data-index="${index}"]`);
            const selectedCinema = cinemaSelect.value;

            Array.from(screenSelect.options).forEach(function(option) {
                if (!option.value) return; // always keep the placeholder visible

                const matches = option.dataset.cinema === selectedCinema;
                option.hidden = selectedCinema !== '' && !matches;
            });

            const currentOption = screenSelect.options[screenSelect.selectedIndex];
            if (currentOption && currentOption.hidden) {
                screenSelect.value = '';
            }
        }

        document.querySelectorAll('.showtime-cinema').forEach(function(cinemaSelect) {
            cinemaSelect.addEventListener('change', function() {
                filterScreensForRow(this);
            });

            // Re-apply filtering on page load in case of validation-error redisplay
            if (cinemaSelect.value) {
                filterScreensForRow(cinemaSelect);
            }
        });
    </script>
@endsection
