@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Select Your Seats</h1>
        </div>
    </div>

    <div class="row">
        <!-- Showtime Info & Price Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm sticky-top">
                <div class="card-body">
                    <!-- Movie & Screen Info -->
                    <h5 class="card-title">{{ $showtime->movie->title }}</h5>
                    <p class="text-muted small mb-3">
                        <strong>Screen:</strong> {{ $showtime->screen->screen_name }}<br>
                        <strong>Cinema:</strong> {{ $showtime->screen->cinema->cinema_name }}<br>
                        <strong>Time:</strong> {{ $showtime->start_time->format('M d, Y H:i') }}
                    </p>

                    <hr>

                    <!-- Price Section (★ DYNAMIC) -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Ticket Price</h6>
                        
                        <!-- Base Price -->
                        <div class="mb-2">
                            <small class="text-muted d-block">Base Price</small>
                            <span class="small text-decoration-line-through">
                                ¥<span id="base-price-display">{{ $showtime->base_price }}</span>
                            </span>
                        </div>

                        <!-- Current Dynamic Price -->
                        <div class="mb-3">
                            <small class="text-muted d-block">Current Price</small>
                            <h3 class="mb-0" id="current-price-display">
                                ¥<span id="current-price-value">{{ $showtime->current_dynamic_price }}</span>
                            </h3>
                            <small class="text-muted">
                                <span id="price-change-badge" class="badge bg-secondary">
                                    No change yet
                                </span>
                            </small>
                        </div>

                        <!-- Occupancy Rate -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Occupancy</small>
                            <div class="progress" style="height: 20px;">
                                <div id="occupancy-bar" class="progress-bar" role="progressbar" 
                                    style="width: 0%"
                                    aria-valuenow="0" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <span id="occupancy-percent">0%</span>
                                </div>
                            </div>
                            <small class="text-muted" id="occupancy-text">0 seats booked</small>
                        </div>

                        <!-- Price Info -->
                        <div class="alert alert-info small mb-0">
                            <strong>💡 Price Update:</strong>
                            <span id="price-info-text">Loading price information...</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Selected Seats Summary -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Selected Seats</h6>
                        <div id="selected-seats-list" class="small">
                            <span class="text-muted">None selected yet</span>
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="bg-light p-3 rounded mb-3">
                        <small class="text-muted d-block">Total Price</small>
                        <h4 class="mb-0">¥<span id="total-price">0</span></h4>
                    </div>

                    <!-- Next Button -->
                    <form action="{{ route('reservations.seat-selection.store') }}" method="POST" id="seat-form">
                        @csrf
                        <input type="hidden" name="selectedSeats" id="selectedSeatsInput" value="[]">
                        <button type="submit" class="btn btn-primary w-100" id="next-button" disabled>
                            Next → Ticket Type
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Seat Selection Grid -->
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-4">Choose Your Seats</h6>
                    
                    <!-- Seat Grid (from original template) -->
                    <div class="seat-selection-grid">
                        <!-- Original seat grid HTML goes here -->
                        <!-- Your existing seat selection code -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript: Real-time Dynamic Pricing -->
<script>
const SHOWTIME_ID = '{{ $showtime->id }}';
const API_ENDPOINT = `/api/dynamic-pricing/${SHOWTIME_ID}`;
const UPDATE_INTERVAL = 5000; // 5 seconds

let pricingData = null;
let selectedSeats = [];

// Fetch dynamic pricing data from API
async function fetchDynamicPricing() {
    try {
        const response = await fetch(API_ENDPOINT);
        if (!response.ok) throw new Error('Failed to fetch pricing');
        
        pricingData = await response.json();
        updatePricingDisplay();
    } catch (error) {
        console.error('Error fetching dynamic pricing:', error);
        document.getElementById('price-info-text').textContent = 'Unable to load price info';
    }
}

// Update display with latest pricing data
function updatePricingDisplay() {
    if (!pricingData) return;

    // Update prices
    document.getElementById('base-price-display').textContent = Math.round(pricingData.base_price);
    document.getElementById('current-price-value').textContent = Math.round(pricingData.current_dynamic_price);

    // Update occupancy bar
    const occupancyPercent = Math.round(pricingData.occupancy_percent);
    document.getElementById('occupancy-bar').style.width = occupancyPercent + '%';
    document.getElementById('occupancy-bar').setAttribute('aria-valuenow', occupancyPercent);
    document.getElementById('occupancy-percent').textContent = occupancyPercent + '%';
    document.getElementById('occupancy-text').textContent = 
        `${pricingData.booked_seats} seats booked out of ${pricingData.capacity}`;

    // Update price change badge
    const changeBadge = document.getElementById('price-change-badge');
    const changePercent = pricingData.price_change_percent;
    const badgeClass = changePercent < -5 ? 'bg-success' : 
                       changePercent > 5 ? 'bg-danger' : 'bg-secondary';
    changeBadge.className = 'badge ' + badgeClass;
    changeBadge.textContent = (changePercent >= 0 ? '+' : '') + changePercent.toFixed(1) + '%';

    // Update price info text
    const indicator = pricingData.price_indicator.toUpperCase();
    const infoText = `Occupancy: ${occupancyPercent}% | Price: ${indicator} | Elasticity: ${pricingData.elasticity_factor.toFixed(2)}`;
    document.getElementById('price-info-text').textContent = infoText;

    // Recalculate total
    updateTotalPrice();
}

// Update total price based on selected seats
function updateTotalPrice() {
    if (!pricingData) return;

    const currentPrice = pricingData.current_dynamic_price;
    const totalPrice = selectedSeats.length * currentPrice;
    
    document.getElementById('total-price').textContent = Math.round(totalPrice);

    // Enable/disable next button
    const nextButton = document.getElementById('next-button');
    nextButton.disabled = selectedSeats.length === 0;
}

// Handle seat selection (integrate with your existing seat selection logic)
function onSeatSelect(seatNumber, seatPrice, isPremium = false) {
    selectedSeats.push({
        seat: seatNumber,
        price: pricingData.current_dynamic_price, // Use dynamic price!
        premium: isPremium
    });

    document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
    updateSeatsList();
    updateTotalPrice();
}

function onSeatDeselect(seatNumber) {
    selectedSeats = selectedSeats.filter(s => s.seat !== seatNumber);
    document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
    updateSeatsList();
    updateTotalPrice();
}

function updateSeatsList() {
    const listEl = document.getElementById('selected-seats-list');
    if (selectedSeats.length === 0) {
        listEl.innerHTML = '<span class="text-muted">None selected yet</span>';
    } else {
        const seats = selectedSeats.map(s => s.seat).join(', ');
        listEl.innerHTML = `<strong>${seats}</strong>`;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initial fetch
    fetchDynamicPricing();

    // Refresh every 5 seconds
    setInterval(fetchDynamicPricing, UPDATE_INTERVAL);

    // Handle form submission
    document.getElementById('seat-form').addEventListener('submit', function(e) {
        if (selectedSeats.length === 0) {
            e.preventDefault();
            alert('Please select at least one seat');
        }
    });
});
</script>

<style>
#current-price-display {
    color: #0d6efd;
    font-weight: 600;
}

.sticky-top {
    top: 100px;
}

.seat-selection-grid {
    /* Your existing seat grid styles */
}
</style>

@endsection
