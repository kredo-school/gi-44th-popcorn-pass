@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Revenue</div>
                <div class="fs-3 fw-bold">$284,500</div>
                <div class="text-success small">+12.3%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Total Users</div>
                <div class="fs-3 fw-bold">18,420</div>
                <div class="text-success small">+8.7%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Active Movies</div>
                <div class="fs-3 fw-bold">24</div>
                <div class="text-success small">+3</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 text-center">
                <div class="text-secondary small mb-1">Reservations Today</div>
                <div class="fs-3 fw-bold">342</div>
                <div class="text-success small">+15.2%</div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend + Top Performing Movies -->
    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <div class="card card-dark p-3" style="height: 320px;">
                <div class="text-warning fw-bold mb-2">Revenue Trend</div>
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-dark p-3" style="height: 320px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-warning fw-bold">Top Performing Movies</span>
                    <span class="text-secondary small">Period: 2026-06-04 ~ 2026-06-10</span>
                </div>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>1. Midnight Express</span>
                        <span class="text-warning">$42,300</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>2. Stellar Voyage</span>
                        <span class="text-warning">$38,100</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>3. The Last Curtain</span>
                        <span class="text-warning">$31,750</span>
                    </li>
                    <li class="d-flex justify-content-between border-bottom border-secondary py-2">
                        <span>4. Neon Dreams</span>
                        <span class="text-warning">$28,400</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span>5. Golden Hour</span>
                        <span class="text-warning">$24,900</span>
                    </li>
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
                    <li class="border-bottom border-secondary py-2">Tanaka S. | Midnight Express | Screen 3 | 18:30</li>
                    <li class="border-bottom border-secondary py-2">Yamamoto K. | Stellar Voyage | Screen 1 | 19:00</li>
                    <li class="border-bottom border-secondary py-2">Suzuki M. | Neon Dreams | Screen 5 | 20:15</li>
                    <li class="py-2">Ito R. | The Last Curtain | Screen 2 | 21:00</li>
                </ul>
            </div>
        </div>
        <div class="col-md-5 d-flex flex-column gap-3">
            <div class="card card-dark p-3">
                <div class="text-warning fw-bold mb-2">Quick Actions</div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-warning btn-sm">+ Add Movie</button>
                    <button class="btn btn-outline-light btn-sm">View Reservations</button>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueTrendChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [180000, 195000, 210000, 225000, 250000, 284500],
                borderColor: '#FFD700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#c9ccd6' }, grid: { color: '#2c3252' } },
                y: { ticks: { color: '#c9ccd6' }, grid: { color: '#2c3252' } }
            }
        }
    });
</script>
@endsection