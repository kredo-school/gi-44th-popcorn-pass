@extends('layouts.app')

@section('title', 'Edit Dynamic Pricing')

@section('content')
<div class="container-fluid mt-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-5 fw-bold">Edit Dynamic Pricing</h1>
            <p class="text-muted">
                <strong>Movie:</strong> {{ $showtime->movie->title }} | 
                <strong>Screen:</strong> {{ $showtime->screen->screen_name }} | 
                <strong>Time:</strong> {{ $showtime->start_time->format('M d, Y H:i') }}
            </p>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors!</strong>
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

    <div class="row">
        <!-- Form Column -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Pricing Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dynamic-pricing.update', $showtime->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Base Price -->
                        <div class="mb-4">
                            <label for="base_price" class="form-label fw-bold">Base Price (¥)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">¥</span>
                                <input 
                                    type="number" 
                                    class="form-control @error('base_price') is-invalid @enderror" 
                                    id="base_price" 
                                    name="base_price"
                                    value="{{ old('base_price', $showtime->base_price) }}"
                                    step="100"
                                    min="0"
                                    max="50000"
                                    required
                                    oninput="updatePreview()">
                            </div>
                            <small class="text-muted d-block mt-2">
                                The original ticket price before dynamic adjustment
                            </small>
                            @error('base_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Elasticity Factor -->
                        <div class="mb-4">
                            <label for="elasticity_factor" class="form-label fw-bold">
                                Elasticity Factor: <span id="elasticity_value">{{ $showtime->elasticity_factor }}</span>
                            </label>
                            <input 
                                type="range" 
                                class="form-range" 
                                id="elasticity_factor" 
                                name="elasticity_factor"
                                value="{{ old('elasticity_factor', $showtime->elasticity_factor) }}"
                                min="0"
                                max="2"
                                step="0.01"
                                oninput="updateElasticityValue(); updatePreview()">
                            <small class="text-muted d-block mt-2">
                                Price sensitivity to occupancy (0.0 = no change, 2.0 = very sensitive)
                            </small>

                            <!-- Preset Buttons -->
                            <div class="mt-3">
                                <label class="form-label text-muted small">Quick Presets:</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                    onclick="setElasticity(0.25)">Conservative</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                    onclick="setElasticity(0.50)">Moderate</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    onclick="setElasticity(1.00)">Aggressive</button>
                            </div>
                        </div>

                        <!-- Current Stats -->
                        <div class="bg-light p-3 rounded mb-4">
                            <h6 class="mb-3">Current Occupancy</h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar" role="progressbar" 
                                    style="width: {{ $occupancyRate * 100 }}%"
                                    aria-valuenow="{{ round($occupancyRate * 100) }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ round($occupancyRate * 100) }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ $showtime->booked_seats }} seats booked out of {{ $showtime->capacity }} total
                            </small>
                        </div>

                        <!-- Last Update -->
                        @if ($showtime->last_price_update)
                            <div class="alert alert-info small mb-4">
                                <strong>Last Updated:</strong> 
                                {{ $showtime->last_price_update->diffForHumans() }}
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                            <a href="{{ route('admin.dynamic-pricing') }}" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-md-6">
            <!-- Price Preview Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Price Preview</h5>
                </div>
                <div class="card-body">
                    <!-- Base Price Display -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-3">Base Price</h6>
                        <div class="display-4 fw-bold">
                            ¥<span id="preview_base_price">{{ $showtime->base_price }}</span>
                        </div>
                        <small class="text-muted">Original ticket price</small>
                    </div>

                    <!-- Formula Display -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-3">Formula</h6>
                        <p class="small mb-1">
                            Current Price = Base Price × (1 + (Occupancy × Elasticity))
                        </p>
                        <p class="small text-success mb-0">
                            Current Price = ¥<span id="preview_base_price_calc">{{ $showtime->base_price }}</span> × 
                            (1 + (<span id="preview_occupancy">{{ round($occupancyRate, 2) }}</span> × 
                            <span id="preview_elasticity">{{ $showtime->elasticity_factor }}</span>))
                        </p>
                    </div>

                    <!-- Calculated Price -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted mb-3">Calculated Current Price</h6>
                        <div class="display-5 fw-bold text-primary">
                            ¥<span id="preview_current_price">{{ $showtime->current_dynamic_price }}</span>
                        </div>
                        <small class="text-muted">
                            Change: <span id="preview_change_percent" class="fw-bold">{{ $priceChangePercent }}%</span>
                        </small>
                    </div>

                    <!-- Price Bounds -->
                    <div class="mb-0">
                        <h6 class="text-muted mb-3">Price Bounds</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Minimum (85%)</small>
                                <strong>¥<span id="preview_min_price">{{ $showtime->base_price * 0.85 }}</span></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Maximum (150%)</small>
                                <strong>¥<span id="preview_max_price">{{ $showtime->base_price * 1.50 }}</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">💡 Tips</h6>
                    <ul class="small text-muted mb-0">
                        <li>Higher elasticity = more price variation based on demand</li>
                        <li>Conservative (0.25): Slight price adjustments</li>
                        <li>Moderate (0.50): Balanced price changes</li>
                        <li>Aggressive (1.00): Strong price variations</li>
                        <li>Prices are automatically bounded between 85%-150% of base price</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateElasticityValue() {
    const value = document.getElementById('elasticity_factor').value;
    document.getElementById('elasticity_value').textContent = parseFloat(value).toFixed(2);
}

function setElasticity(value) {
    document.getElementById('elasticity_factor').value = value;
    updateElasticityValue();
    updatePreview();
}

function updatePreview() {
    const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
    const elasticity = parseFloat(document.getElementById('elasticity_factor').value) || 0;
    const occupancy = {{ $occupancyRate }};

    // Update base price displays
    document.getElementById('preview_base_price').textContent = Math.round(basePrice);
    document.getElementById('preview_base_price_calc').textContent = Math.round(basePrice);

    // Update elasticity and occupancy in formula
    document.getElementById('preview_elasticity').textContent = elasticity.toFixed(2);
    document.getElementById('preview_occupancy').textContent = occupancy.toFixed(2);

    // Calculate current price
    const multiplier = 1 + (occupancy * elasticity);
    let currentPrice = basePrice * multiplier;

    // Apply bounds
    const minPrice = basePrice * 0.85;
    const maxPrice = basePrice * 1.50;
    currentPrice = Math.max(minPrice, Math.min(maxPrice, currentPrice));

    // Update preview
    document.getElementById('preview_current_price').textContent = Math.round(currentPrice);
    document.getElementById('preview_min_price').textContent = Math.round(minPrice);
    document.getElementById('preview_max_price').textContent = Math.round(maxPrice);

    // Calculate and display percentage change
    const changePercent = basePrice > 0 ? ((currentPrice - basePrice) / basePrice) * 100 : 0;
    const changeElement = document.getElementById('preview_change_percent');
    changeElement.textContent = (changePercent >= 0 ? '+' : '') + changePercent.toFixed(1) + '%';
    
    // Change color based on change
    if (changePercent < -5) {
        changeElement.className = 'fw-bold text-success';
    } else if (changePercent > 5) {
        changeElement.className = 'fw-bold text-danger';
    } else {
        changeElement.className = 'fw-bold text-secondary';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
});
</script>

@endsection
