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
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <div class="col-md-6 mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select">
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                    <option value="Archived">Archived</option>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label">Published Date</label>
                <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at') }}">
                <div class="form-text text-light">
                    This information will be published automatically on the selected date only if the status is set to <strong>Published</strong>.
                </div>
            </div>
        
            <div class="col-md-6 mb-4">
                <label class="form-label">Image URL</label>
                <input type="text" name="image_url" class="form-control" value="{{ old('image_url') }}">
            </div>
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