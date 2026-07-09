@extends('layouts.admin')

@section('title', 'Add Information')
@section('page-title', 'Add Information')

@section('content')

<div class="card card-dark p-4">

    <form action="{{ route('admin.information.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title') }}" required>
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Content <span class="text-danger">*</span></label>
            <textarea name="content" rows="8" class="form-control @error('content') is-invalid @enderror"
                required>{{ old('content') }}</textarea>

            @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>

                <select name="category" class="form-select">
                    <option value="General">General</option>
                    <option value="Promotion">Promotion</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Event">Event</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>

                <select name="status" class="form-select">
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                </select>
            </div>

        </div>

        <div class="mb-4">
            <label class="form-label">Published Date</label>

            <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at') }}">
        </div>

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('admin.information') }}" class="btn btn-outline-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-warning">
                Save Information
            </button>

        </div>

    </form>

</div>

@endsection