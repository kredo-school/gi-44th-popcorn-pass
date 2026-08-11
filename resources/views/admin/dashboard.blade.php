@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

    {{-- Dashboard Header / Filters --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">

        <div>
            <div class="text-secondary small mb-1">
                Dashboard Overview
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <h1 class="text-white mb-0">
                    {{ $thisYear }}
                </h1>

                @if ($selectedCinema)
                    <span class="badge rounded-pill text-bg-warning px-3 py-2">
                        {{ $selectedCinema->cinema_name }}
                    </span>
                @else
                    <span class="badge rounded-pill border border-secondary text-light px-3 py-2">
                        All Cinemas
                    </span>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <div class="d-flex flex-column flex-md-row gap-3">

            {{-- Cinema Filter --}}
            <div style="min-width: 260px;">
                <label
                    for="cinemaSelector"
                    class="form-label text-secondary small mb-1"
                >
                    Cinema
                </label>

                <select
                    id="cinemaSelector"
                    class="form-select bg-dark text-white border-secondary"
                >
                    <option
                        value=""
                        @selected(!$cinemaId)
                    >
                        All Cinemas
                    </option>

                    @foreach ($cinemas as $cinema)
                        <option
                            value="{{ $cinema->id }}"
                            @selected((string) $cinemaId === (string) $cinema->id)
                        >
                            {{ $cinema->cinema_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Year Filter --}}
            <div style="min-width: 150px;">
                <label
                    for="yearSelector"
                    class="form-label text-secondary small mb-1"
                >
                    Year
                </label>

                <select
                    id="yearSelector"
                    class="form-select bg-dark text-white border-secondary"
                >
                    @foreach ($availableYears as $year)
                        <option
                            value="{{ $year }}"
                            @selected((int) $thisYear === (int) $year)
                        >
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>


    {{-- KPI Cards --}}
    <div class="row g-3 mb-3">

        {{-- Total Revenue --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Total Revenue
                </div>

                <div class="fs-3 fw-bold">
                    ${{ number_format($thisYearRevenue, 2) }}
                </div>

                <div class="small {{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $revenueChange >= 0 ? '+' : '' }}
                    {{ number_format($revenueChange, 1) }}%

                    <span class="text-secondary">
                        vs {{ $lastYear }}
                    </span>
                </div>

            </div>
        </div>


        {{-- Customers --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    {{ $selectedCinema ? 'Customers' : 'Total Users' }}
                </div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalUsers) }}
                </div>

                <div class="small {{ $userChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $userChange >= 0 ? '+' : '' }}
                    {{ number_format($userChange, 1) }}%

                    <span class="text-secondary">
                        vs {{ $lastYear }}
                    </span>
                </div>

            </div>
        </div>


        {{-- Active Movies --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Active Movies
                </div>

                <div class="fs-3 fw-bold">
                    {{ number_format($activeMovies) }}
                </div>

                <div class="small text-secondary">
                    Now showing
                </div>

            </div>
        </div>


        {{-- Total Reservations --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Total Reservations
                </div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalReservations) }}
                </div>

                <div class="small {{ $reservationChange >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $reservationChange >= 0 ? '+' : '' }}
                    {{ number_format($reservationChange, 1) }}%

                    <span class="text-secondary">
                        vs {{ $lastYear }}
                    </span>
                </div>

            </div>
        </div>

    </div>


    {{-- Revenue Trend + Top Performing Movies --}}
    <div class="row g-3 mb-3">

        {{-- Revenue Trend --}}
        <div class="col-lg-7">
            <div class="card card-dark p-3 h-100">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <div class="text-warning fw-bold">
                            Revenue Trend
                        </div>

                        <div class="text-secondary small">
                            {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                        </div>
                    </div>

                    <span class="text-secondary small">
                        {{ $thisYear }}
                    </span>

                </div>

                <div style="height: 260px;">
                    <canvas
                        id="revenueTrendChart"
                        data-revenue='@json($revenueData)'
                    ></canvas>
                </div>

            </div>
        </div>


        {{-- Top Performing Movies --}}
        <div class="col-lg-5">
            <div class="card card-dark p-3 h-100">

                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">

                    <div>
                        <div class="text-warning fw-bold">
                            Top Performing Movies
                        </div>

                        <div class="text-secondary small">
                            {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                        </div>
                    </div>

                    <span class="text-secondary small text-nowrap">
                        {{ $thisYear }}
                    </span>

                </div>

                <ul class="list-unstyled mb-0">

                    @forelse ($movieSalesRanking as $index => $movie)

                        <li class="d-flex justify-content-between align-items-center gap-3 border-bottom border-secondary py-2">

                            <span class="text-truncate">
                                {{ $index + 1 }}. {{ $movie->title }}
                            </span>

                            <span class="text-warning text-nowrap">
                                ${{ number_format($movie->total_sales, 2) }}
                            </span>

                        </li>

                    @empty

                        <li class="py-4 text-center text-secondary">
                            No sales data available.
                        </li>

                    @endforelse

                </ul>

            </div>
        </div>

    </div>


    {{-- Recent Reservations + Quick Actions --}}
    <div class="row g-3">

        {{-- Recent Reservations --}}
        <div class="col-lg-7">
            <div class="card card-dark p-3 h-100">

                <div class="d-flex justify-content-between align-items-center mb-2">

                    <div>
                        <div class="text-warning fw-bold">
                            Recent Reservations
                        </div>

                        <div class="text-secondary small">
                            {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                            · {{ $thisYear }}
                        </div>
                    </div>

                </div>

                <ul class="list-unstyled mb-0">

                    @forelse ($recentReservations as $reservation)

                        <li class="border-bottom border-secondary py-2">

                            <div class="d-flex flex-column flex-md-row justify-content-between gap-1">

                                <div>

                                    <span class="fw-semibold">
                                        {{ $reservation->user?->username ?? 'Guest' }}
                                    </span>

                                    <span class="text-secondary">
                                        ·
                                        {{ $reservation->movie?->title ?? 'Movie unavailable' }}
                                    </span>

                                </div>

                                <div class="text-secondary small text-md-end">

                                    {{ $reservation->cinema?->cinema_name ?? 'Cinema unavailable' }}

                                    @if ($reservation->screen)
                                        · Screen {{ $reservation->screen->screen_number }}
                                    @endif

                                    @if ($reservation->showtime?->start_time)
                                        · {{ $reservation->showtime->start_time->format('H:i') }}
                                    @endif

                                </div>

                            </div>

                        </li>

                    @empty

                        <li class="py-4 text-center text-secondary">
                            No reservations found for {{ $thisYear }}.
                        </li>

                    @endforelse

                </ul>

            </div>
        </div>


        {{-- Right Column --}}
        <div class="col-lg-5 d-flex flex-column gap-3">

            {{-- Quick Actions --}}
            <div class="card card-dark p-3">

                <div class="text-warning fw-bold mb-2">
                    Quick Actions
                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <a
                        href="{{ route('admin.movies.create') }}"
                        class="btn btn-outline-warning btn-sm"
                    >
                        + Add Movie
                    </a>

                    <a
                        href="{{ route('admin.reservations', array_filter([
                            'cinema_id' => $cinemaId,
                        ])) }}"
                        class="btn btn-outline-light btn-sm"
                    >
                        View Reservations
                    </a>

                </div>

            </div>


            {{-- Current Filter --}}
            <div class="card card-dark p-3">

                <div class="text-warning fw-bold mb-2">
                    Current View
                </div>

                <div class="small">

                    <div class="d-flex justify-content-between py-1">

                        <span class="text-secondary">
                            Cinema
                        </span>

                        <span>
                            {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                        </span>

                    </div>

                    <div class="d-flex justify-content-between py-1">

                        <span class="text-secondary">
                            Year
                        </span>

                        <span>
                            {{ $thisYear }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const cinemaSelector = document.getElementById('cinemaSelector');
            const yearSelector = document.getElementById('yearSelector');


            /**
             * Redirect while preserving dashboard filters.
             */
            function updateDashboardFilters() {

                const url = new URL(
                    '{{ route('admin.dashboard') }}',
                    window.location.origin
                );

                const cinemaId = cinemaSelector
                    ? cinemaSelector.value
                    : '';

                const year = yearSelector
                    ? yearSelector.value
                    : '';


                if (cinemaId) {
                    url.searchParams.set(
                        'cinema_id',
                        cinemaId
                    );
                }


                if (year) {
                    url.searchParams.set(
                        'year',
                        year
                    );
                }


                window.location.href = url.toString();
            }


            if (cinemaSelector) {

                cinemaSelector.addEventListener(
                    'change',
                    updateDashboardFilters
                );

            }


            if (yearSelector) {

                yearSelector.addEventListener(
                    'change',
                    updateDashboardFilters
                );

            }

        });
    </script>

@endsection
