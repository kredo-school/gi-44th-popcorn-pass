@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="text-muted">
            <h1 class="text-white">{{ $thisYear }}</h1>
        </div>

        {{-- Total Revenue --}}
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Revenue</div>

                <div class="fs-3 fw-bold">
                    ${{ number_format($thisYearRevenue) }}
                </div>

                <div class="small {{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $revenueChange >= 0 ? '+' : '' }}{{ number_format($revenueChange, 1) }}%
                </div>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Users</div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalUsers) }}
                </div>

                <div class="small {{ $userChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $userChange >= 0 ? '+' : '' }}{{ number_format($userChange, 1) }}%
                </div>
            </div>
        </div>

        {{-- Total Movies --}}
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Movies</div>

                <div class="fs-3 fw-bold">
                    {{ number_format($activeMovies) }}
                </div>

                <div class="small {{ $movieChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $movieChange >= 0 ? '+' : '' }}{{ number_format($movieChange, 1) }}%
                </div>
            </div>
        </div>

        {{-- Total Reservations --}}
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Reservations</div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalReservations) }}
                </div>

                <div class="small {{ $reservationChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $reservationChange >= 0 ? '+' : '' }}{{ number_format($reservationChange, 1) }}%
                </div>
            </div>
        </div>

    </div>


    <!-- Revenue Trend + Top Performing Movies -->
    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <div class="card card-dark p-3" style="height:320px;">
                <div class="text-warning fw-bold mb-2">
                    Revenue Trend
                </div>

                <canvas id="revenueTrendChart" data-revenue='@json($revenueData)'></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-dark p-3" style="height: 320px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-warning fw-bold">Top Performing Movies</span>
                    <span class="text-secondary small">Period: {{ $thisYear }}-01-01 ~
                        {{ $thisYear }}-12-31</span>
                </div>
                <ul class="list-unstyled">

                    @foreach ($movieSalesRanking as $index => $movie)
                        <li class="d-flex justify-content-between border-bottom border-secondary py-2">

                            <span>
                                {{ $index + 1 }}. {{ $movie->title }}
                            </span>

                            <span class="text-warning">
                                ${{ number_format($movie->total_sales) }}
                            </span>


                        </li>
                    @endforeach

                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Reservations + Quick Actions / Notifications -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-2">Recent Reservations</div>

                <ul class="list-unstyled mb-0">

                    @forelse ($recentReservations as $reservation)
                        <li class="border-bottom border-secondary py-2">

                            {{ $reservation->user?->username ?? 'Guest' }}
                            |
                            {{ $reservation->movie->title }}
                            |
                            Screen {{ $reservation->screen->screen_number }}
                            |
                            {{ \Carbon\Carbon::parse($reservation->showtime->start_time)->format('H:i') }}

                        </li>

                    @empty

                        <li class="py-2 text-secondary">
                            No reservations found.
                        </li>
                    @endforelse

                </ul>
            </div>
        </div>
        <div class="col-md-5 d-flex flex-column gap-3">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-2">Quick Actions</div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.movies.create') }}" class="btn btn-outline-warning btn-sm">+ Add Movie</a>
                    <a href="#" class="btn btn-outline-light btn-sm">View Reservations</a>
                </div>
            </div>
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-2">Notifications</div>
                <ul class="list-unstyled mb-0 small">
                    <li class="py-1">Screen 4 projector maintenance due</li>
                    <li class="py-1">New user registrations +23% today</li>
                    <li class="py-1">Stellar Voyage showing sold out</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
