@extends('layouts.admin')

@section('title', 'Edit Information')
@section('page-title', 'Edit Information')

@section('content')

<div class="card card-dark p-4">

    <form action="{{ route('admin.information.update', $information->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">
                Title <span class="text-danger">*</span>
            </label>

            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                value="{{ old('title', $information->title) }}" required>

            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">
                Content <span class="text-danger">*</span>
            </label>

            <textarea name="content" rows="8" class="form-control @error('content') is-invalid @enderror"
                required>{{ old('content', $information->content) }}</textarea>

            @error('content')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>

                <select name="category" class="form-select @error('category') is-invalid @enderror">
                    <option value="General" {{ old('category', $information->category) == 'General' ? 'selected' : ''
                        }}>
                        General
                    </option>

                    <option value="Promotion" {{ old('category', $information->category) == 'Promotion' ? 'selected' :
                        '' }}>
                        Promotion
                    </option>

                    <option value="Maintenance" {{ old('category', $information->category) == 'Maintenance' ? 'selected'
                        : '' }}>
                        Maintenance
                    </option>

                    <option value="Event" {{ old('category', $information->category) == 'Event' ? 'selected' : '' }}>
                        Event
                    </option>
                </select>

                @error('category')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>

                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="Draft" {{ old('status', $information->status) == 'Draft' ? 'selected' : '' }}>
                        Draft
                    </option>

                    <option value="Published" {{ old('status', $information->status) == 'Published' ? 'selected' : ''
                        }}>
                        Published
                    </option>
                </select>

                @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>

        <div class="mb-4">
            <label class="form-label">Published Date</label>

            <input type="datetime-local" name="published_at"
                class="form-control @error('published_at') is-invalid @enderror"
                value="{{ old('published_at', optional($information->published_at)->format('Y-m-d\TH:i')) }}">

            @error('published_at')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('admin.information') }}" class="btn btn-outline-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-warning">
                Update Information
            </button>

        </div>

    </form>

</div>

@endsection
