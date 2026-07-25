@extends('layouts.admin')

@section('title', 'Coupons & Promotions')
@section('page-title', 'Coupons & Promotions')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item text-white" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-coupons" type="button">Coupons</button>
        </li>
        <li class="nav-item text-white" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-promotions" type="button">Promotions</button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ===================== COUPONS TAB ===================== --}}
        <div class="tab-pane fade show active" id="tab-coupons" role="tabpanel">

            <div class="card card-dark p-3 mb-3">
                <button class="btn btn-outline-warning mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#add-coupon-form">
                    + Add Coupon
                </button>

                <div class="collapse" id="add-coupon-form">
                    <form method="POST" action="{{ route('admin.coupons.store') }}" class="row g-3 mb-2">
                        @csrf

                        <div class="col-md-3">
                            <label class="form-label text-secondary small">Coupon Code</label>
                            <input type="text" name="code" class="form-control" placeholder="SUMMER2026" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">Type</label>
                            <select name="coupon_type" class="form-select" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Discount %</label>
                            <input type="number" name="discount_percent" class="form-control" min="1" max="100">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Discount $</label>
                            <input type="number" step="0.01" name="discount_amount" class="form-control" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Max Uses</label>
                            <input type="number" name="max_uses" class="form-control" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">Expires At</label>
                            <input type="date" name="expires_at" class="form-control">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">Save Coupon</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-dark p-3">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Discount</th>
                            <th>Uses</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->code }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $coupon->coupon_type)) }}</td>
                                <td>
                                    @if ($coupon->discount_percent)
                                        {{ $coupon->discount_percent }}%
                                    @elseif ($coupon->discount_amount)
                                        ${{ number_format($coupon->discount_amount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $coupon->current_uses }} / {{ $coupon->max_uses ?? '∞' }}</td>
                                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '—' }}</td>
                                <td>
                                    @if ($coupon->coupon_status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($coupon->coupon_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon->id) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $coupon->coupon_status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $coupon->coupon_status === 'active' ? 'Disable' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>

        {{-- ===================== PROMOTIONS TAB ===================== --}}
        <div class="tab-pane fade" id="tab-promotions" role="tabpanel">

            <div class="card card-dark p-3 mb-3">
                <button class="btn btn-outline-warning mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#add-promotion-form">
                    + Add Promotion
                </button>

                <div class="collapse" id="add-promotion-form">
                    <form method="POST" action="{{ route('admin.promotions.store') }}" class="row g-3 mb-2">
                        @csrf

                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed_amount">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Discount Value</label>
                            <input type="number" step="0.01" name="discount_value" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-secondary small">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Applicable Genre (optional)</label>
                            <select name="applicable_genre_id" class="form-select">
                                <option value="">All genres</option>
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}">{{ $genre->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Applicable Movie (optional)</label>
                            <select name="applicable_movie_id" class="form-select">
                                <option value="">All movies</option>
                                @foreach ($movies as $movie)
                                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small">Applicable Cinema (optional)</label>
                            <select name="applicable_cinema_id" class="form-select">
                                <option value="">All cinemas</option>
                                @foreach ($cinemas as $cinema)
                                    <option value="{{ $cinema->id }}">{{ $cinema->cinema_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-secondary small">Max Uses</label>
                            <input type="number" name="max_uses" class="form-control" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-secondary small">Min Ticket Purchase</label>
                            <input type="number" name="min_ticket_purchase" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary small">Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">Save Promotion</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-dark p-3">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Period</th>
                            <th>Uses</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->title }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $promotion->type)) }}</td>
                                <td>
                                    {{ $promotion->type === 'percentage' ? $promotion->discount_value . '%' : '$' . number_format($promotion->discount_value, 2) }}
                                </td>
                                <td>{{ $promotion->start_date->format('Y-m-d') }} – {{ $promotion->end_date->format('Y-m-d') }}</td>
                                <td>{{ $promotion->current_uses }} / {{ $promotion->max_uses ?? '∞' }}</td>
                                <td>
                                    @if ($promotion->promotion_status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($promotion->promotion_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.promotions.toggle-status', $promotion->id) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $promotion->promotion_status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $promotion->promotion_status === 'active' ? 'Disable' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">No promotions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $promotions->links() }}
            </div>
        </div>

    </div>

@endsection