@extends('admin.layouts.master')

@section('title', $order ? 'Edit Order' : 'Create Order')

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
                                        <li>0-5 km: RM 3.00</li>
                                        <li>5-10 km: RM 5.00</li>
                                        <li>10-15 km: RM 8.00</li>
                                    </ul>
                                </small>
                            </div>
                            <input type="hidden" name="delivery_charge" id="delivery-charge-input" value="0">
                        </div>

                        <div class="form-group mb-4">
                            <label class="d-block mb-3">Select Services</label>
                            <div class="alert alert-info">
                                <i class="tim-icons icon-alert-circle-exc"></i>
                                One load = 10 pieces of clothing
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input service-checkbox" 
                                       id="washing" name="washing" value="1"
                                       data-price="10"
                                       {{ $order && $order->washing ? 'checked' : '' }}>
                                <label class="custom-control-label" for="washing">
                                    Washing (RM 10 per load)
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input service-checkbox" 
                                       id="ironing" name="ironing" value="1"
                                       data-price="8"
                                       {{ $order && $order->ironing ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ironing">
                                    Ironing (RM 8 per load)
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input service-checkbox" 
                                       id="dry_cleaning" name="dry_cleaning" value="1"
                                       data-price="15"
                                       {{ $order && $order->dry_cleaning ? 'checked' : '' }}>
                                <label class="custom-control-label" for="dry_cleaning">
                                    Dry Cleaning (RM 15 per load)
                                </label>
                            </div>
                            @error('services')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4" id="extraLoadSection" style="display: none;">
                            <label class="d-block mb-3">Additional Clothes</label>
                            <div class="alert alert-warning">
                                <ul class="mb-0">
                                    <li>Base price includes first 10 pieces</li>
                                    <li>1-10 additional pieces: +RM 15</li>
                                    <li>More than 10 additional pieces: +RM 25</li>
                                </ul>
                            </div>
                            
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input load-radio" 
                                       id="no_extra" name="extra_load" value="none" checked
                                       data-price="0"
                                       {{ $order && $order->extra_load === 'none' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="no_extra">
                                    No additional clothes (1-10 pieces)
                                </label>
                            </div>
                            
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input load-radio" 
                                       id="extra_small" name="extra_load" value="small"
                                       data-price="15"
                                       {{ $order && $order->extra_load === 'small' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="extra_small">
                                    11-20 pieces (+RM 15)
                                </label>
                            </div>
                            
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input load-radio" 
                                       id="extra_large" name="extra_load" value="large"
                                       data-price="25"
                                       {{ $order && $order->extra_load === 'large' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="extra_large">
                                    More than 20 pieces (+RM 25)
                                </label>
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
    const checkboxes = document.querySelectorAll('.service-checkbox');
    const loadRadios = document.querySelectorAll('.load-radio');
    const totalInput = document.getElementById('total');
    const extraLoadSection = document.getElementById('extraLoadSection');
    const providerSelect = document.getElementById('provider_id');
    const providerDistanceSpan = document.getElementById('provider-distance');
    const deliveryChargeSpan = document.getElementById('delivery-charge');
    const deliveryChargeInput = document.getElementById('delivery-charge-input');

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
            // For distances > 15km, you might want to show an error or handle differently
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
        
        calculateTotal();
    }

    function calculateTotal() {
        let baseTotal = 0;
        let hasServices = false;

        // Calculate services total
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                hasServices = true;
                baseTotal += parseFloat(checkbox.dataset.price);
            }
        });

        // Show/hide extra load section
        extraLoadSection.style.display = hasServices ? 'block' : 'none';

        // Add extra load cost
        if (hasServices) {
            const selectedLoad = document.querySelector('input[name="extra_load"]:checked');
            if (selectedLoad) {
                baseTotal += parseFloat(selectedLoad.dataset.price);
            }
        }

        // Add delivery charge
        baseTotal += parseFloat(deliveryChargeInput.value);

        totalInput.value = baseTotal.toFixed(2);
    }

    // Add event listeners
    providerSelect.addEventListener('change', updateDeliveryInfo);
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    loadRadios.forEach(radio => {
        radio.addEventListener('change', calculateTotal);
    });

    // Initial calculations
    updateDeliveryInfo();
    calculateTotal();
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