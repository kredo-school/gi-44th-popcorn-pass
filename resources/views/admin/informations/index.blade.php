@extends('layouts.admin')

@section('title', 'Information')
@section('page-title', 'Information Management')

@section('content')
    
    @if (session('success'))
        <div class="alert alert-success">{{ session('sucess')}}</div>
    @endif

    <div class="d-flex gap-2 mb-3">
        <input type="text" class="form-control" placeholder="Search information..." style="max-width:250;">

        <select class="form-select" style="max-150px;">
            <option>Status: ALL</option>
            <option>Published</option>
            <option>Draft</option>
        </select>

        <div class="ms-auto d-flex gap-2">
            <a href="#" class="btn btn-outline-warning">+ Add Information</a>

            <a href="#" class="btn btn-outline-light">Edit Information</a>

            <a href="#" class="btn btn-outline-danger">Delete Information</a>
        </div>
    </div>

    <div class="row g-3">

        {{-- Information List --}}
        <div class="col-md-8">
            <div class="card card-dark p-3">
                <table class="table table-dark table-hover align-miggle mb-0">
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
                           <tr class="information-row" data-information-id='{{ $info->id}}' style="cursor:pointer">
                                <td>
                                    <input type="checkbox">
                                </td>
                                <td>{{ $info->title }}</td>
                                <td>{{ $info->category ?? 'General' }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $info->status }}
                                    </span>
                                </td>
                            </tr> 
                        @empty
                            <tr>
                                <td class="col-5 text-center text-secondary py-4">
                                    No information found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $information->link() }}
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
                    <div class="form-controll bg-transparent" id="detail-title">
                        -
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">
                        Category
                    </label>
                    <div class="form-controll bg-transparent" id="detail-category">
                        -
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">
                        Status
                    </label>
                    <div class="form-controll bg-transparent" id="detail-status">
                        -
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-secondary small">
                        Content
                    </label>
                    <div class="form-controll bg-transparent" id="detail-content" style="min-height: 220px;">
                        -
                    </div>
                </div>
            </div>
        </div>


    </div>
    


@endsection