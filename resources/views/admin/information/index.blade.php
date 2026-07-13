@extends('layouts.admin')

@section('title', 'Information')
@section('page-title', 'Information Management')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="gap-2 mb-3">
    <form method="GET" action="{{ route('admin.information') }}" class="d-flex gap-2 mb-3 align-items-center">
    
        <input type="text" name="search" class="form-control" placeholder="Search information..." style="max-width: 250px;"
            value="{{ request('search') }}">
    
        <select name="category" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('category', 'all' )=='all' ? 'selected' : '' }}>Category: All</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    
        <select name="status" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
            <option value="all" {{ request('status', 'all' )=='all' ? 'selected' : '' }}>Status: All</option>
            <option value="Published" {{ request('status')=='Published' ? 'selected' : '' }}>Published</option>
            <option value="Draft" {{ request('status')=='Draft' ? 'selected' : '' }}>Draft</option>
        </select>
    
        <button type="submit" class="btn btn-outline-warning">Search</button>
    
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.information.create') }}" class="btn btn-outline-warning">
                + Add Information
            </a>
            <a href="#" id="edit-information-btn" class="btn btn-outline-light disabled">
                Edit Information
            </a>
            <button type="button" id="delete-information-btn" class="btn btn-outline-danger disabled"
                onclick="confirmDelete()">
                Delete Information
            </button>
        </div>
    
    </form>
    
    <form id="delete-information-form" method="POST">
        @csrf
        @method('DELETE')
    </form>
    

</div>

<div class="row g-3">

    {{-- Information List --}}
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

                        <tr class="information-row" data-information-id="{{ $info->id }}" style="cursor:pointer;">
                            <td>
                                <input type="checkbox">
                            </td>
                            <td>{{ $info->title }}</td>
                            <td>{{ $info->category->name }}</td>
                            <td>
                                <span class="badge {{ $info->status === 'Published' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $info->status }}
                                </span>
                            </td>
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

        <div class="mt-3">
            {{ $information->links() }}
        </div>

    </div>

    {{-- Information Details --}}
    <div class="col-md-4">
        <div class="card card-dark p-3">
            <div class="text-warning fw-bold mb-3">
                Information Details
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary small">
                    Title
                </label>

                <div class="form-control bg-transparent text-white" id="detail-title">
                    —
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary small">
                    Category
                </label>

                <div class="form-control bg-transparent text-white" id="detail-category">
                    —
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary small">
                    Status
                </label>

                <div class="form-control bg-transparent text-white" id="detail-status">
                    —
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-secondary small">
                    Published Date
                </label>

                <div class="form-control bg-transparent text-white" id="detail-published-at">
                    —
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label text-secondary small">
                    Content
                </label>

                <div class="form-control bg-transparent text-white" id="detail-content" style="min-height:220px;">
                    —
                </div>
            </div>

        </div>

        {{-- Category Management --}}
        <div class="card card-dark p-3">
            <div class="text-warning fw-bold mb-3">Manage Categories</div>
        
            @if(session('error'))
                <div class="alert alert-danger py-1 small">{{ session('error') }}</div>
            @endif
        
            {{-- Add Category Form --}}
            <form action="{{ route('admin.information.categories.store') }}" method="POST" class="mb-3">
                @csrf
                <div class="d-flex gap-2 mb-2">
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Category name" required>
                    <input type="color" name="color" class="form-control form-control-color form-control-sm" value="#6C757D"
                        title="Pick color">
                    <button type="submit" class="btn btn-warning btn-sm">Add</button>
                </div>
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </form>
        
            {{-- Category List --}}
            <table class="table table-dark table-sm align-middle mb-0">
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>
                                <span class="badge" style="background-color: {{ $cat->color }}">
                                    {{ $cat->name }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.information.categories.delete', $cat->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-secondary py-2">No categories.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const editButton = document.querySelector('#edit-information-btn');
        const deleteForm = document.querySelector('#delete-information-form');
        const deleteButton = document.querySelector('#delete-information-btn');

        document.querySelectorAll('.information-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.type === 'checkbox') return;

                const informationId = this.dataset.informationId;

                if (editButton) {
                    editButton.href = `/admin/information/${informationId}/edit`;
                    editButton.classList.remove('disabled');
                }

                if (deleteForm && deleteButton) {
                    deleteForm.action = `/admin/information/${informationId}`;
                    deleteButton.classList.remove('disabled');
                }

                fetch(`/admin/information/${informationId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        document.querySelector('#detail-title').textContent = data.title || '—';
                        document.querySelector('#detail-category').textContent = data.category || '—';
                        document.querySelector('#detail-status').textContent = data.status || '—';
                        document.querySelector('#detail-content').textContent = data.content || '—';
                        document.querySelector('#detail-published-at').textContent = data.published_at
                            ? data.published_at.substring(0, 10) : '—';
                    });
            });
        });

    });

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this information?')) {
            document.getElementById('delete-information-form').submit();
        }
    }

</script>
@endsection
