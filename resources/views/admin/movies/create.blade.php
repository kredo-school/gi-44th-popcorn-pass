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
                            <label class="form-label text-secondary small">
                                Genre <span class="text-muted">(Select up to 3)</span>
                            </label>

                            <div class="row">
                                @foreach ($genres as $genre)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input genre-checkbox" type="checkbox"
                                                name="genre_ids[]" value="{{ $genre->id }}" id="genre{{ $genre->id }}"
                                                {{ in_array($genre->id, old('genre_ids', [])) ? 'checked' : '' }}>

                                            <label class="form-check-label" for="genre{{ $genre->id }}">
                                                {{ $genre->title }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-text">
                                You can select up to <strong>3</strong> genres.
                            </div>

                            <div class="form-text">
                                Hold <strong>Ctrl</strong> (Windows) or <strong>⌘ Command</strong> (Mac) to select multiple
                                genres.
                            </div>
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
                            <input type="text" name="director" class="form-control"
                                placeholder="Enter director name..." value="{{ old('director') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Cast Members</label>
                            <input type="text" name="cast" class="form-control"
                                placeholder="Enter cast members..." value="{{ old('cast') }}">
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

                    <div class="text-warning fw-bold mb-3">
                        Generate Showtimes
                    </div>

                    <div class="text-secondary small mb-3">
                        Set a recurrence pattern to automatically generate showtimes for this movie.
                    </div>

                    <div class="row g-3">

                        {{-- Cinema --}}
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">
                                Cinema
                            </label>

                            <select name="showtime_generate[cinema_id]" class="form-select showtime-generate-cinema">

                                <option value="">Select Cinema...</option>

                                @foreach ($cinemas as $cinema)
                                    <option value="{{ $cinema->id }}"
                                        {{ old('showtime_generate.cinema_id') == $cinema->id ? 'selected' : '' }}>
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

                            <select name="showtime_generate[screen_id]" class="form-select showtime-generate-screen">

                                <option value="">Select Screen...</option>

                                @foreach ($screens as $screen)
                                    <option value="{{ $screen->id }}" data-cinema="{{ $screen->cinema_id }}"
                                        {{ old('showtime_generate.screen_id') == $screen->id ? 'selected' : '' }}>

                                        Screen {{ $screen->screen_number }}
                                        - {{ $screen->screen_type }}

                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Start Date --}}
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">
                                Start Date
                            </label>

                            <input type="date" name="showtime_generate[start_date]" class="form-control"
                                value="{{ old('showtime_generate.start_date') }}">
                        </div>

                        {{-- End Date --}}
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">
                                End Date
                            </label>

                            <input type="date" name="showtime_generate[end_date]" class="form-control"
                                value="{{ old('showtime_generate.end_date') }}">
                        </div>

                        {{-- Days --}}
                        <div class="col-12">
                            <label class="form-label text-secondary small">
                                Days of Week
                            </label>

                            <div class="d-flex gap-3 flex-wrap">

                                @foreach ([
            'Sun' => 0,
            'Mon' => 1,
            'Tue' => 2,
            'Wed' => 3,
            'Thu' => 4,
            'Fri' => 5,
            'Sat' => 6,
        ] as $label => $value)
                                    <div class="form-check">

                                        <input class="form-check-input" type="checkbox" name="showtime_generate[days][]"
                                            value="{{ $value }}" id="day{{ $value }}"
                                            {{ in_array($value, old('showtime_generate.days', [])) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="day{{ $value }}">
                                            {{ $label }}
                                        </label>

                                    </div>
                                @endforeach

                            </div>
                        </div>

                        {{-- Time Slots --}}
                        <div class="col-12">

                            <label class="form-label text-secondary small">
                                Time Slots (Max 6)
                            </label>

                            <div class="row g-2">

                                @for ($i = 0; $i < 6; $i++)
                                    <div class="col-md-2">

                                        <input type="time" name="showtime_generate[slots][]" class="form-control"
                                            value="{{ old('showtime_generate.slots.' . $i) }}">

                                    </div>
                                @endfor

                            </div>

                        </div>

                    </div>

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
    {{-- select multiple genres --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const genreSelect = document.querySelector('select[name="genre_ids[]"]');

            genreSelect.addEventListener('change', function() {
                const selected = [...this.selectedOptions];

                if (selected.length > 3) {
                    alert('You can select up to 3 genres.');

                    selected[selected.length - 1].selected = false;
                }
            });
        });
    </script>
@endsection
