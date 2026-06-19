@extends('layouts.admin')

@section('title', 'Movies')
@section('page-title', 'Movie Management')

@section('content')

    <div class="d-flex gap-2 mb-3">
        <input type="text" class="form-control" placeholder="Search movies..." style="max-width: 250px;">
        <select class="form-select" style="max-width: 150px;">
            <option>Genre: All</option>
        </select>
        <select class="form-select" style="max-width: 150px;">
            <option>Status: All</option>
        </select>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.movies.create') }}" class="btn btn-outline-warning">+ Add Movie</a>
            <a href="#" class="btn btn-outline-light">Edit Movie</a>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movies as $movie)
                            <tr class="movie-row" data-movie-id="{{ $movie->id }}" style="cursor: pointer;">
                                <td><input type="checkbox"></td>
                                <td>{{ $movie->title }}</td>
                                <td>{{ $movie->genre->title ?? '—' }}</td>
                                <td>{{ $movie->duration }} min</td>
                                <td class="text-warning">{{ $movie->review_average ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $movie->status }}</span>
                                </td>
                                <td>{{ $movie->released_date ? $movie->released_date->format('Y-m-d') : '—' }}</td>
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
                    <div class="form-control bg-transparent" id="detail-title">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Synopsis</label>
                    <div class="form-control bg-transparent" id="detail-synopsis" style="min-height: 60px;">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Director</label>
                    <div class="form-control bg-transparent" id="detail-director">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Cast</label>
                    <div class="form-control bg-transparent" id="detail-cast">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">Trailer URL</label>
                    <div class="form-control bg-transparent" id="detail-trailer">—</div>
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

@section('scripts')
<script>
document.querySelectorAll('.movie-row').forEach(function (row) {
    row.addEventListener('click', function () {
        const movieId = this.dataset.movieId;
        fetch(`/admin/movies/${movieId}/details`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('#detail-title').textContent = data.title || '—';
                document.querySelector('#detail-synopsis').textContent = data.synopsis || '—';
                document.querySelector('#detail-director').textContent = data.director || '—';
                document.querySelector('#detail-cast').textContent = data.cast || '—';
                document.querySelector('#detail-trailer').textContent = data.trailer_url || '—';
            });
    });
});
</script>
@endsection