@extends('layouts.admin')

@section('title', 'Information')
@section('page-title', 'Information Management')

@section('content')

    {{-- ========================================
        SUCCESS MESSAGE
    ======================================== --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ========================================
        SEARCH / FILTER
    ======================================== --}}
    <div class="gap-2 mb-3">

        <form method="GET" action="{{ route('admin.information') }}" class="d-flex gap-2 mb-3 align-items-center">

            {{-- Search --}}
            <input type="text" name="search" class="form-control information-search" placeholder="Search information..."
                value="{{ request('search') }}">

            {{-- Category --}}
            <select name="category" class="form-select information-select" onchange="this.form.submit()">
                <option value="all" {{ request('category', 'all') == 'all' ? 'selected' : '' }}>
                    Category: All
                </option>

                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Status --}}
            <select name="status" class="form-select information-select" onchange="this.form.submit()">
                <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>
                    Status: All
                </option>

                <option value="Published" {{ request('status') == 'Published' ? 'selected' : '' }}>
                    Published
                </option>

                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>
                    Draft
                </option>

                <option value="Archived" {{ request('status') == 'Archived' ? 'selected' : '' }}>
                    Archived
                </option>
            </select>

            {{-- Search Button --}}
            <button type="submit" class="btn btn-outline-warning">
                Search
            </button>


            {{-- Reset --}}
            @if (request()->filled('search') || request('category', 'all') !== 'all' || request('status', 'all') !== 'all')
                <a href="{{ route('admin.information') }}" class="btn btn-outline-light">
                    Reset
                </a>
            @endif


            {{-- ========================================
                ACTION BUTTONS
            ======================================== --}}
            <div class="ms-auto d-flex gap-2">

                {{-- Add --}}
                <a href="{{ route('admin.information.create') }}" class="btn btn-outline-warning">
                    + Add Information
                </a>

                {{-- Edit --}}
                <a href="#" id="edit-information-btn" class="btn btn-outline-light disabled">
                    Edit Information
                </a>

                {{-- Delete --}}
                <button type="button" id="delete-information-btn" class="btn btn-outline-danger disabled"
                    onclick="confirmDelete()">
                    Delete Information
                </button>

            </div>

        </form>


        {{-- ========================================
            DELETE FORM
        ======================================== --}}
        <form id="delete-information-form" method="POST">
            @csrf
            @method('DELETE')
        </form>

    </div>


    {{-- ========================================
        MAIN CONTENT
    ======================================== --}}
    <div class="row g-3">


        {{-- ========================================
            INFORMATION LIST
        ======================================== --}}
        <div class="col-md-8">

            <div class="card card-dark p-3">

                <table class="table table-dark table-hover align-middle mb-0">

                    <thead>
                        <tr>
                            <th></th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published At</th>
                        </tr>
                    </thead>


                    <tbody>

                        @forelse ($information as $info)
                            <tr class="information-row" data-information-id="{{ $info->id }}">

                                {{-- Checkbox --}}
                                <td>
                                    <input type="checkbox">
                                </td>


                                {{-- Title --}}
                                <td>
                                    {{ $info->title }}
                                </td>


                                {{-- Category --}}
                                <td>

                                    @if ($info->category)
                                        <span class="badge"
                                            style="
                                                background-color: {{ $info->category->color }};
                                                color: #fff;
                                            ">
                                            {{ $info->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            No Category
                                        </span>
                                    @endif

                                </td>


                                {{-- Status --}}
                                <td>

                                    <span class="badge {{ $info->status_badge_class }}">
                                        {{ $info->status }}
                                    </span>

                                </td>


                                {{-- Published Date --}}
                                <td>
                                    {{ $info->published_at ? $info->published_at->format('Y-m-d') : '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    No information found.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="mt-3">
                {{ $information->links() }}
            </div>

        </div>


        {{-- ========================================
            INFORMATION DETAILS
        ======================================== --}}
        <div class="col-md-4">

            <div class="card card-dark p-3">

                <div class="text-warning fw-bold mb-2">
                    Information Details
                </div>


                {{-- Title --}}
                <div class="mb-2">

                    <label class="form-label text-secondary small">
                        Title
                    </label>

                    <div class="form-control bg-transparent text-white" id="detail-title">
                        —
                    </div>

                </div>


                {{-- Category --}}
                <div class="mb-2">

                    <label class="form-label text-secondary small">
                        Category
                    </label>

                    <div class="form-control bg-transparent text-white" id="detail-category">
                        —
                    </div>

                </div>


                {{-- Status --}}
                <div class="mb-2">

                    <label class="form-label text-secondary small">
                        Status
                    </label>

                    <div class="form-control bg-transparent text-white" id="detail-status">
                        —
                    </div>

                </div>


                {{-- Published Date --}}
                <div class="mb-2">

                    <label class="form-label text-secondary small">
                        Published Date
                    </label>

                    <div class="form-control bg-transparent text-white" id="detail-published-at">
                        —
                    </div>

                </div>


                {{-- Image --}}
                <div class="mb-2">

                    <label class="form-label text-secondary small">
                        Image
                    </label>

                    <div id="detail-image" class="text-center">
                        <span class="text-white">
                            No Image
                        </span>
                    </div>

                </div>


                {{-- Content --}}
                <div class="mb-0">

                    <label class="form-label text-secondary small">
                        Content
                    </label>

                    <div class="form-control bg-transparent text-white detail-content" id="detail-content">
                        —
                    </div>

                </div>

            </div>


            {{-- ========================================
                CATEGORY MANAGEMENT
            ======================================== --}}
            <div class="card card-dark p-3 mt-1">

                <div class="text-warning fw-bold mb-3">
                    Manage Categories
                </div>


                {{-- Error Message --}}
                @if (session('error'))
                    <div class="alert alert-danger py-1 small">
                        {{ session('error') }}
                    </div>
                @endif


                {{-- ========================================
                    ADD CATEGORY
                ======================================== --}}
                <form action="{{ route('admin.information.categories.store') }}" method="POST" class="mb-3">

                    @csrf

                    <div class="d-flex gap-2 mb-2">

                        <input type="text" name="name" class="form-control form-control-sm"
                            placeholder="Category name" required>

                        <input type="color" name="color" class="form-control form-control-color form-control-sm"
                            value="#6C757D" title="Pick color">

                        <button type="submit" class="btn btn-warning btn-sm">
                            Add
                        </button>

                    </div>


                    {{-- Validation Error --}}
                    @error('name')
                        <div class="text-danger small">
                            {{ $message }}
                        </div>
                    @enderror

                </form>


                {{-- ========================================
                    CATEGORY LIST
                ======================================== --}}
                <div class="category-list-scroll">

                    <table class="table table-dark table-sm align-middle mb-0">

                        <tbody>

                            @forelse ($categories as $cat)
                                <tr>

                                    {{-- Category Name --}}
                                    <td>

                                        <span class="badge"
                                            style="
                                                background-color: {{ $cat->color }};
                                                color: #fff;
                                            ">
                                            {{ $cat->name }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end">

                                        {{-- Edit --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $cat->id }}">
                                            Edit
                                        </button>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('admin.information.categories.delete', $cat->id) }}"
                                            method="POST" class="d-inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>


                                {{-- Edit Category Modal --}}
                                @include('admin.information.modals.edit-category', [
                                    'category' => $cat,
                                ])

                            @empty

                                <tr>

                                    <td colspan="2" class="text-center text-secondary py-2">
                                        No categories.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection
