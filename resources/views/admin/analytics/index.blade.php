@extends('layouts.admin')

@section('title', 'Revenue Analytics')
@section('page-title', 'Revenue Analytics')

@section('content')

    {{-- Analytics Header / Filters --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">

        <div>
            <div class="text-secondary small mb-1">
                Analytics Overview
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <h1 class="text-white mb-0">
                    {{ $selectedYear }}
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
                            @selected((int) $selectedYear === (int) $year)
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
                    ${{ number_format($totalRevenue, 2) }}
                </div>

                <div class="small text-secondary">
                    Paid payments in {{ $selectedYear }}
                </div>

            </div>
        </div>


        {{-- Paid Reservations --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Paid Reservations
                </div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalReservations) }}
                </div>

                <div class="small text-secondary">
                    Completed payments
                </div>

            </div>
        </div>


        {{-- Customers --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Customers
                </div>

                <div class="fs-3 fw-bold">
                    {{ number_format($totalCustomers) }}
                </div>

                <div class="small text-secondary">
                    Unique registered customers
                </div>

            </div>
        </div>


        {{-- Avg Revenue per Reservation --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card card-dark p-3 text-center h-100">

                <div class="text-secondary small mb-1">
                    Avg Revenue / Reservation
                </div>

                <div class="fs-3 fw-bold">
                    ${{ number_format($avgRevenuePerReservation, 2) }}
                </div>

                <div class="small text-secondary">
                    Paid revenue average
                </div>

            </div>
        </div>

    </div>


    {{-- Revenue Trend + Reservation Trend --}}
    <div class="row g-3 mb-3">

        {{-- Monthly Revenue Trend --}}
        <div class="col-lg-7">
            <div class="card card-dark p-3 h-100">

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>
                        <div class="text-warning fw-bold">
                            Monthly Revenue Trend
                        </div>

                        <div class="text-secondary small">
                            {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                        </div>
                    </div>

                    <span class="text-secondary small">
                        {{ $selectedYear }}
                    </span>

                </div>

                <div style="height: 300px;">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>

            </div>
        </div>


        {{-- Reservation Trend --}}
        <div class="col-lg-5">
            <div class="card card-dark p-3 h-100">

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <div>
                        <div class="text-warning fw-bold">
                            Reservation Trend
                        </div>

                        <div class="text-secondary small">
                            Paid reservations by month
                        </div>
                    </div>

                    <span class="text-secondary small">
                        {{ $selectedYear }}
                    </span>

                </div>

                <div style="height: 300px;">
                    <canvas id="reservationTrendChart"></canvas>
                </div>

            </div>
        </div>

    </div>


    {{-- Top Performing Movies --}}
    <div class="row g-3 mb-3">

        <div class="col-12">
            <div class="card card-dark p-3">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">

                    <div>
                        <div class="text-warning fw-bold">
                            Top Performing Movies
                        </div>

                        <div class="text-secondary small">
                            Revenue generated from paid reservations
                        </div>
                    </div>

                    <div class="text-secondary small">
                        {{ $selectedCinema?->cinema_name ?? 'All Cinemas' }}
                        · {{ $selectedYear }}
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th style="width: 70px;">Rank</th>
                                <th>Movie</th>
                                <th class="text-end">Reservations</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($topMovies as $index => $movie)

                                <tr>
                                    <td>
                                        <span
                                            class="badge {{ $index === 0 ? 'text-bg-warning' : 'border border-secondary text-light' }}"
                                        >
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $movie->title }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($movie->reservation_count) }}
                                    </td>

                                    <td class="text-end text-warning fw-semibold">
                                        ${{ number_format($movie->total_revenue, 2) }}
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center text-secondary py-4"
                                    >
                                        No movie revenue data available for {{ $selectedYear }}.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>


    {{-- Cinema Performance --}}
    @if (!$cinemaId)

        <div class="row g-3 mb-3">

            <div class="col-12">
                <div class="card card-dark p-3">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">

                        <div>
                            <div class="text-warning fw-bold">
                                Cinema Performance
                            </div>

                            <div class="text-secondary small">
                                Compare all cinema locations
                            </div>
                        </div>

                        <div class="text-secondary small">
                            {{ $selectedYear }}
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">

                            <thead>
                                <tr>
                                    <th style="width: 70px;">Rank</th>
                                    <th>Cinema</th>
                                    <th class="text-end">Customers</th>
                                    <th class="text-end">Reservations</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($cinemaPerformance as $index => $cinema)

                                    <tr>

                                        <td>
                                            <span
                                                class="badge {{ $index === 0 ? 'text-bg-warning' : 'border border-secondary text-light' }}"
                                            >
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>

                                        <td class="fw-semibold">
                                            {{ $cinema->cinema_name }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($cinema->customer_count) }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($cinema->reservation_count) }}
                                        </td>

                                        <td class="text-end text-warning fw-semibold">
                                            ${{ number_format($cinema->total_revenue, 2) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="5"
                                            class="text-center text-secondary py-4"
                                        >
                                            No cinema performance data available for {{ $selectedYear }}.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>

    @endif


    {{-- Current View --}}
    <div class="row g-3">

        <div class="col-lg-5 ms-lg-auto">

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
                            {{ $selectedYear }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between py-1">
                        <span class="text-secondary">
                            Revenue Source
                        </span>

                        <span>
                            Paid Payments
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | Dashboard Filters
            |--------------------------------------------------------------------------
            */

            const cinemaSelector = document.getElementById('cinemaSelector');
            const yearSelector = document.getElementById('yearSelector');

            function updateAnalyticsFilters() {

                const url = new URL(
                    '{{ route('admin.analytics') }}',
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
                    updateAnalyticsFilters
                );
            }

            if (yearSelector) {
                yearSelector.addEventListener(
                    'change',
                    updateAnalyticsFilters
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Chart Defaults
            |--------------------------------------------------------------------------
            */

            if (typeof Chart === 'undefined') {
                return;
            }

            Chart.defaults.color = '#adb5bd';
            Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.08)';

            const monthLabels = [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ];

            const monthlyRevenueData =
                @json($monthlyRevenueData);

            const monthlyReservationData =
                @json($monthlyReservationData);


            /*
            |--------------------------------------------------------------------------
            | Monthly Revenue Chart
            |--------------------------------------------------------------------------
            */

            const monthlyRevenueCanvas =
                document.getElementById('monthlyRevenueChart');

            if (monthlyRevenueCanvas) {

                new Chart(
                    monthlyRevenueCanvas,
                    {
                        type: 'line',

                        data: {
                            labels: monthLabels,

                            datasets: [
                                {
                                    label: 'Revenue',

                                    data: monthlyRevenueData,

                                    borderColor: '#FFD700',

                                    backgroundColor:
                                        'rgba(255, 215, 0, 0.12)',

                                    pointBackgroundColor: '#FFD700',

                                    pointBorderColor: '#FFD700',

                                    pointRadius: 4,

                                    pointHoverRadius: 6,

                                    borderWidth: 3,

                                    fill: true,

                                    tension: 0.35
                                }
                            ]
                        },

                        options: {
                            responsive: true,

                            maintainAspectRatio: false,

                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },

                            plugins: {
                                legend: {
                                    display: false
                                },

                                tooltip: {
                                    callbacks: {
                                        label: function (context) {

                                            const value =
                                                Number(context.raw || 0);

                                            return '$' +
                                                value.toLocaleString(
                                                    undefined,
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                );
                                        }
                                    }
                                }
                            },

                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },

                                y: {
                                    beginAtZero: true,

                                    ticks: {
                                        callback: function (value) {
                                            return '$' +
                                                Number(value)
                                                    .toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Reservation Trend Chart
            |--------------------------------------------------------------------------
            */

            const reservationTrendCanvas =
                document.getElementById('reservationTrendChart');

            if (reservationTrendCanvas) {

                new Chart(
                    reservationTrendCanvas,
                    {
                        type: 'bar',

                        data: {
                            labels: monthLabels,

                            datasets: [
                                {
                                    label: 'Reservations',

                                    data: monthlyReservationData,

                                    backgroundColor:
                                        'rgba(255, 215, 0, 0.75)',

                                    borderColor: '#FFD700',

                                    borderWidth: 1,

                                    borderRadius: 5,

                                    maxBarThickness: 34
                                }
                            ]
                        },

                        options: {
                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {
                                legend: {
                                    display: false
                                }
                            },

                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },

                                y: {
                                    beginAtZero: true,

                                    ticks: {
                                        precision: 0,
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    }
                );
            }

        });
    </script>

@endsection