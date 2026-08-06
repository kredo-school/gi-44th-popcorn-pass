@extends('layouts.admin')

@section('title', 'Movies')
@section('page-title', 'Movie Management')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex gap-2 mb-3">
        <input type="text" class="form-control" placeholder="Search movies..." style="max-width: 250px;">
        <select class="form-select" style="max-width: 150px;">
            <option>Genre: All</option>
        </select>
        <select class="form-select" style="max-width: 150px;">
            <option>Status: All</option>
        </select>
        <button type="submit" class="btn btn-outline-warning">Search</button>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.movies.create') }}" class="btn btn-outline-warning">+ Add Movie</a>
            <a href="#" id="edit-movie-btn" class="btn btn-outline-light disabled">Edit Movie</a>
            <a href="#" class="btn btn-outline-danger">Archive Movie</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card card-dark p-3">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Movie Title</th>
                            <th>Genre</th>
                            <th>Duration</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Release Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movies as $movie)
                            <tr class="movie-row" data-movie-id="{{ $movie->id }}" style="cursor: pointer;">
                                <td><input type="checkbox"></td>
                                <td>{{ $movie->title }}</td>
                                <td>
                                    {{ $movie->genres->pluck('title')->join(', ') ?: '—' }}
                                </td>
                                <td>{{ $movie->duration }} min</td>
                                <td class="text-warning">{{ $movie->review_average ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $movie->status }}</span>
                                </td>
                                <td>{{ $movie->released_date ? $movie->released_date->format('Y-m-d') : '—' }}</td>
                                <td>{{ $movie->end_date ? $movie->end_date->format('Y-m-d') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">No movies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $movies->links() }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-3">Movie Details</div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Title</label>
                    <div class="form-control bg-transparent text-white" id="detail-title">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Synopsis</label>
                    <div class="form-control bg-transparent text-white" id="detail-synopsis" style="min-height: 60px;">—
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Director</label>
                    <div class="form-control bg-transparent text-white" id="detail-director">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Cast</label>
                    <div class="form-control bg-transparent text-white" id="detail-cast">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Trailer URL</label>
                    <div class="form-control bg-transparent text-white" id="detail-trailer">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Poster Upload</label>
                    <div class="border border-secondary rounded p-3 text-center text-secondary small">
                        Drag &amp; Drop or Click to Upload
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label text-secondary small">Banner Upload</label>
                    <div class="border border-secondary rounded p-3 text-center text-secondary small">
                        Drag &amp; Drop or Click to Upload
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


