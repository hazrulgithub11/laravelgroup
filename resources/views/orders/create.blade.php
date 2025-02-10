@extends('admin.layouts.master')

@section('title', $order ? 'Edit Order' : 'Create Order')

@push('css')
<style>
/* Override theme colors */
body, .wrapper, .main-panel, .content {
    background: #ffffff !important;
    color: #000000 !important;
}

.card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.card-header {
    border-bottom: 1px solid #e8e8e8;
}

.card-title {
    color: #000000 !important;
    font-weight: 600;
}

/* Form styles */
.form-control {
    border: 1px solid #e8e8e8;
    color: #000000 !important;
    background: #ffffff !important;
}

.form-control:focus {
    border-color: #1E856D;
}

label {
    color: #000000 !important;
    font-weight: 500;
}

/* Alert styles */
.alert-info {
    background: #f8f9fa !important;
    border: 1px solid #e8e8e8;
    color: #000000 !important;
}

.alert-primary {
    background: #f8f9fa !important;
    border: 1px solid #e8e8e8;
    color: #000000 !important;
}

/* Button styles */
.btn-primary {
    background: #1E856D !important;
    border: none;
}

.btn-secondary {
    background: #6c757d !important;
    border: none;
}

/* Badge styles */
.badge {
    padding: 0.5em 1em;
}

.badge-primary {
    background: #1E856D !important;
}

/* Icon colors */
.tim-icons {
    color: #1E856D !important;
}

/* List styles */
.list-unstyled {
    color: #000000 !important;
}

/* Price and cost displays */
#delivery-charge,
#service-cost,
#total-categories,
#total {
    color: #000000 !important;
    font-weight: 500;
}

/* Select dropdown */
select.form-control {
    color: #000000 !important;
    background-color: #ffffff !important;
}

select.form-control option {
    color: #000000 !important;
    background-color: #ffffff !important;
}

/* Input group */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #e8e8e8;
    color: #000000;
}

/* Error messages */
.invalid-feedback {
    color: #dc3545 !important;
}

/* Distance and provider info */
#provider-distance {
    color: #000000 !important;
}

</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $order ? 'Edit Order #' . $order->id : 'Create New Order' }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" 
                          action="{{ $order ? route('orders.update', $order) : route('orders.store') }}" 
                          id="orderForm">
                        @csrf
                        @if($order)
                            @method('PUT')
                        @endif
                        
                        <div class="form-group mb-4">
                            <label for="provider_id">Select Provider</label>
                            <select name="provider_id" id="provider_id" class="form-control @error('provider_id') is-invalid @enderror" required>
                                <option value="">Choose a provider...</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" 
                                            data-distance="{{ $provider->distance ?? 'N/A' }}"
                                            data-categories="{{ json_encode($provider->categories) }}"
                                            {{ ($order ? $order->provider_id : $selectedProviderId) == $provider->id ? 'selected' : '' }}>
                                        {{ $provider->name }} 
                                        @if(isset($provider->distance))
                                            ({{ $provider->distance }} km away)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('provider_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="d-block">Delivery Charges</label>
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="tim-icons icon-delivery-fast"></i> Distance: 
                                        <span id="provider-distance">Select a provider</span>
                                    </div>
                                    <div>
                                        Charge: <span id="delivery-charge">RM 0.00</span>
                                    </div>
                                </div>
                                <hr>
                                <small>
                                    <ul class="mb-0">
                                    <li style="color: #000000; font-weight: 500;">0-5 km: RM 3.00</li>
                                    <li style="color: #000000; font-weight: 500;">5-10 km: RM 5.00</li>
                                    <li style="color: #000000; font-weight: 500;">10-15 km: RM 8.00</li>
                                    </ul>
                                </small>
                            </div>
                            <input type="hidden" name="delivery_charge" id="delivery-charge-input" value="0">
                        </div>

                        <div class="form-group mb-4">
                            <label class="d-block mb-3">Service Categories</label>
                            <div class="alert alert-info">
                                <i class="tim-icons icon-alert-circle-exc"></i>
                                Each service category costs RM 10
                            </div>
                            
                            <div id="provider-categories" class="mb-3">
                                <!-- Categories will be populated dynamically -->
                            </div>
                            
                            <div class="alert alert-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Total Service Categories:</span>
                                    <span id="total-categories">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Service Cost:</span>
                                    <span id="service-cost">RM 0.00</span>
                                </div>
                            </div>
                        </div>

                        

                        <div class="form-group mb-4">
                            <label class="d-block mb-3">Pickup & Delivery Location</label>
                            <div class="alert alert-info">
                                <i class="tim-icons icon-pin"></i>
                                Your items will be picked up and delivered to the same address
                            </div>

                            <div class="input-group mb-3">
                                <input type="text" id="address" name="address" 
                                       value="{{ $order ? $order->address : old('address') }}"
                                       class="form-control @error('address') is-invalid @enderror"
                                       placeholder="Your address" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" onclick="getCurrentLocation()">
                                        <i class="tim-icons icon-pin"></i> Use Current Location
                                    </button>
                                </div>
                                @error('address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Hidden inputs for coordinates -->
                            <input type="hidden" id="latitude" name="latitude" value="{{ $order ? $order->latitude : old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ $order ? $order->longitude : old('longitude') }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pickup_time">Pickup Date & Time</label>
                                        <input type="datetime-local" id="pickup_time" name="pickup_time"
                                               value="{{ $order ? $order->pickup_time->format('Y-m-d\TH:i') : old('pickup_time') }}"
                                               class="form-control @error('pickup_time') is-invalid @enderror"
                                               min="{{ now()->format('Y-m-d\TH:i') }}"
                                               required>
                                        @error('pickup_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_time">Delivery Date & Time</label>
                                        <input type="datetime-local" id="delivery_time" name="delivery_time"
                                               class="form-control @error('delivery_time') is-invalid @enderror"
                                               min="{{ now()->format('Y-m-d\TH:i') }}"
                                               required>
                                        @error('delivery_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="total">Total Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">RM</span>
                                </div>
                                <input type="number" step="0.01" name="total" id="total" 
                                       class="form-control @error('total') is-invalid @enderror" 
                                       readonly required>
                            </div>
                            @error('total')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $order ? 'Update Order' : 'Create Order' }}
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider_id');
    const providerDistanceSpan = document.getElementById('provider-distance');
    const deliveryChargeSpan = document.getElementById('delivery-charge');
    const deliveryChargeInput = document.getElementById('delivery-charge-input');
    const categoriesDiv = document.getElementById('provider-categories');
    const totalCategoriesSpan = document.getElementById('total-categories');
    const serviceCostSpan = document.getElementById('service-cost');
    const totalInput = document.getElementById('total');

    function calculateDeliveryCharge(distance) {
        if (!distance || distance === 'N/A') return 0;
        
        distance = parseFloat(distance);
        let charge = 0;

        if (distance <= 5) {
            charge = 3;
        } else if (distance <= 10) {
            charge = 5;
        } else if (distance <= 15) {
            charge = 8;
        } else {
            charge = 8;
        }

        return charge;
    }

    function updateDeliveryInfo() {
        const selectedOption = providerSelect.options[providerSelect.selectedIndex];
        const distance = selectedOption.dataset.distance;
        
        if (selectedOption.value) {
            providerDistanceSpan.textContent = distance === 'N/A' ? 'N/A' : `${distance} km`;
            const charge = calculateDeliveryCharge(distance);
            deliveryChargeSpan.textContent = `RM ${charge.toFixed(2)}`;
            deliveryChargeInput.value = charge;
        } else {
            providerDistanceSpan.textContent = 'Select a provider';
            deliveryChargeSpan.textContent = 'RM 0.00';
            deliveryChargeInput.value = 0;
        }

        // Update categories and total after delivery info is updated
        updateCategories();
    }

    function updateCategories() {
        const selectedOption = providerSelect.options[providerSelect.selectedIndex];
        if (!selectedOption.value) {
            categoriesDiv.innerHTML = '<p class="text-muted">Please select a provider</p>';
            totalCategoriesSpan.textContent = '0';
            serviceCostSpan.textContent = 'RM 0.00';
            calculateTotal(0);
            return;
        }

        // Get provider categories from data attribute
        const categories = JSON.parse(selectedOption.dataset.categories || '[]');
        
        // Display categories
        categoriesDiv.innerHTML = categories.map(category => `
            <div class="badge badge-primary mr-2 mb-2">${category}</div>
        `).join('');

        // Update counts and costs
        const categoryCount = categories.length;
        const serviceCost = categoryCount * 10; // RM10 per category
        
        totalCategoriesSpan.textContent = categoryCount;
        serviceCostSpan.textContent = `RM ${serviceCost.toFixed(2)}`;
        
        calculateTotal(serviceCost);
    }

    function calculateTotal(serviceCost) {
        const deliveryCharge = parseFloat(deliveryChargeInput.value) || 0;
        const total = serviceCost + deliveryCharge;
        totalInput.value = total.toFixed(2);
    }

    // Add event listener
    providerSelect.addEventListener('change', updateDeliveryInfo);

    // Initial calculation
    updateDeliveryInfo();
});

function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by this browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        // Success callback
        position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Update hidden inputs
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;

            // Get address from coordinates using reverse geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.display_name;
                    document.getElementById('address').value = address;
                })
                .catch(error => {
                    console.error('Error getting address:', error);
                    alert('Error getting address. Please enter manually.');
                });
        },
        // Error callback
        error => {
            console.error('Geolocation error:', error);
            let message = "An error occurred while getting your location.";
            if (error.code === 1) {
                message = "Please allow location access in your browser settings.";
            }
            alert(message);
        },
        // Options
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );
}

// Set minimum delivery time based on pickup time
document.getElementById('pickup_time').addEventListener('change', function() {
    const pickupTime = new Date(this.value);
    const minDelivery = new Date(pickupTime.getTime() + (24 * 60 * 60 * 1000)); // Add 24 hours
    
    const deliveryInput = document.getElementById('delivery_time');
    deliveryInput.min = minDelivery.toISOString().slice(0, 16);
    
    // If current delivery time is before new minimum, update it
    if (new Date(deliveryInput.value) < minDelivery) {
        deliveryInput.value = minDelivery.toISOString().slice(0, 16);
    }
});
</script>
@endpush 