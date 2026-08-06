@extends('layouts.admin')

@section('title', 'Revenue Analytics')
@section('page-title', 'Revenue Analytics')

@section('content')

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Daily Revenue</div>
                <div class="fs-3 fw-bold">${{ number_format($dailyRevenue, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Weekly Revenue</div>
                <div class="fs-3 fw-bold">${{ number_format($weeklyRevenue, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Monthly Revenue</div>
                <div class="fs-3 fw-bold">${{ number_format($monthlyRevenue, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Avg Ticket Price</div>
                <div class="fs-3 fw-bold">${{ number_format($avgTicketPrice, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Daily Revenue Chart + Top Movies -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card card-dark p-3" style="height: 360px;">
                <div class="text-warning fw-bold mb-2">Daily Revenue</div>
                <canvas id="dailyRevenueChart" data-labels='@json($dailyRevenueChart->pluck("date")->values())'
                    data-values='@json($dailyRevenueChart->pluck("total")->values())'></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-dark p-3" style="height: 360px;">
                <div class="text-warning fw-bold mb-3">Top 5 Movies – Revenue</div>
                <ul class="list-unstyled mb-0">
                    @forelse ($topMovies as $row)
                        <li class="d-flex justify-content-between border-bottom border-secondary py-2">
                            <span>{{ $row->movie->title ?? 'Unknown' }}</span>
                            <span class="text-warning">${{ number_format($row->total_revenue, 2) }}</span>
                        </li>
                    @empty
                        <li class="text-secondary py-4 text-center">No data available.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

@endsection
