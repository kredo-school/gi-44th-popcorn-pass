@extends('layouts.app')

@section('title', 'Dynamic Pricing Management')

@section('content')
<div class="container-fluid mt-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold">Dynamic Pricing Management</h1>
            <p class="text-muted">Manage ticket prices based on real-time occupancy</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Showtimes</h6>
                    <h3 class="fw-bold">{{ $showtimes->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Average Base Price</h6>
                    <h3 class="fw-bold">¥{{ number_format($showtimes->avg('base_price'), 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Average Elasticity</h6>
                    <h3 class="fw-bold">{{ number_format($showtimes->avg('elasticity_factor'), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Average Current Price</h6>
                    <h3 class="fw-bold">¥{{ number_format($showtimes->avg('current_dynamic_price'), 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Showtimes Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Showtimes with Dynamic Pricing</h5>
        </div>
        <div class="card-body p-0">
            @if ($showtimes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Movie</th>
                                <th>Screen</th>
                                <th>Start Time</th>
                                <th>Base Price</th>
                                <th>Current Price</th>
                                <th>Occupancy</th>
                                <th>Elasticity</th>
                                <th>Change %</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($showtimes as $showtime)
                                @php
                                    $occupancyRate = $showtime->capacity > 0 ? ($showtime->booked_seats / $showtime->capacity) : 0;
                                    $priceChangePercent = $showtime->base_price > 0 
                                        ? (($showtime->current_dynamic_price - $showtime->base_price) / $showtime->base_price) * 100 
                                        : 0;
                                    $priceIndicator = '';
                                    if ($priceChangePercent < -5) {
                                        $priceIndicator = 'badge bg-success';
                                    } elseif ($priceChangePercent > 5) {
                                        $priceIndicator = 'badge bg-danger';
                                    } else {
                                        $priceIndicator = 'badge bg-secondary';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $showtime->movie->title }}</strong>
                                    </td>
                                    <td>
                                        {{ $showtime->screen->screen_name }}
                                        <br>
                                        <small class="text-muted">{{ $showtime->screen->cinema->cinema_name }}</small>
                                    </td>
                                    <td>
                                        {{ $showtime->start_time->format('M d, Y H:i') }}
                                    </td>
                                    <td>
                                        <strong>¥{{ number_format($showtime->base_price, 0) }}</strong>
                                    </td>
                                    <td>
                                        <strong class="text-primary">¥{{ number_format($showtime->current_dynamic_price, 0) }}</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $occupancyRate * 100 }}%"
                                                aria-valuenow="{{ round($occupancyRate * 100) }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ round($occupancyRate * 100) }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $showtime->booked_seats }}/{{ $showtime->capacity }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($showtime->elasticity_factor, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="{{ $priceIndicator }}">
                                            {{ $priceChangePercent >= 0 ? '+' : '' }}{{ number_format($priceChangePercent, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.dynamic-pricing.edit', $showtime->id) }}" 
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    {{ $showtimes->links() }}
                </div>
            @else
                <div class="p-5 text-center">
                    <p class="text-muted mb-0">No showtimes available for dynamic pricing management.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">How Dynamic Pricing Works</h5>
                    <p class="text-muted mb-2">
                        <strong>Formula:</strong> Current Price = Base Price × (1 + (Occupancy Rate × Elasticity Factor))
                    </p>
                    <ul class="text-muted small mb-0">
                        <li><strong>Base Price:</strong> The original ticket price (default ¥1,900)</li>
                        <li><strong>Occupancy Rate:</strong> Percentage of seats booked (0.0 to 1.0)</li>
                        <li><strong>Elasticity Factor:</strong> Price sensitivity (0.0 to 2.0)</li>
                        <li><strong>Price Bounds:</strong> Minimum 85% of base price, Maximum 150% of base price</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
