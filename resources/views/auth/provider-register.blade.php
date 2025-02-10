@extends('layouts.app')

@section('content')
<!-- Add Back Button -->
<div class="position-fixed" style="top: 20px; left: 20px; z-index: 1000;">
    <a href="{{ url('/') }}" class="btn btn-link text-dark" style="text-decoration: none;">
        <i class="fas fa-arrow-left"></i>
        <span class="ms-2">Back to Home</span>
    </a>
</div>

<div class="container-fluid" style="width: 100vw; min-height: 100vh; display: flex; align-items: center; background: #f7f7f7;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left side with image -->
            <div class="col-md-6 d-none d-md-block">
                <img src="/images/tasker.jpg" alt="Service Provider" 
                     style="width: 100%; height: auto; border-radius: 1rem; object-fit: cover;">
            </div>
            
            <!-- Right side with initial form -->
            <div class="col-md-6">
                <div style="max-width: 450px; margin: 0 auto; padding: 2rem;">
                    <h1 style="font-size: 2.5rem; font-weight: bold; color: #2f3033; margin-bottom: 1rem;">
                        Earn money your way
                    </h1>
                    <p style="font-size: 1.1rem; color: #5e5e5e; margin-bottom: 2rem;">
                        See how much you can make providing services in your area
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Add this temporarily for debugging -->
                    @if(session('debug'))
                        <div class="alert alert-info">
                            {{ session('debug') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <label style="font-size: 1.1rem; font-weight: 500; color: #2f3033; margin-bottom: 0.5rem;">
                            Choose a Service
                        </label>
                        <div class="dropdown w-100">
                            <button class="btn dropdown-toggle w-100 text-start" type="button" id="serviceDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                                Select a service
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="serviceDropdown" 
                                style="border-radius: 0.5rem; border: 1px solid #ddd; padding: 0.5rem; max-height: 300px; overflow-y: auto;">
                                <li>
                                    <button type="button" class="dropdown-item" onclick="selectService('laundry')">
                                        🧺 Laundry Service
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item" onclick="selectService('gardener')">
                                        🌿 Gardener Service
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item" onclick="selectService('cleaning')">
                                        🧹 Cleaning Service
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div id="categoryContainer" class="mb-4" style="display: none;">
                        <label style="font-size: 1.1rem; font-weight: 500; color: #2f3033; margin-bottom: 0.5rem;">
                            Select Categories
                        </label>
                        <div id="laundryCategories" style="display: none;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="washing_drying" id="washing">
                                <label class="form-check-label" for="washing">Washing & Drying</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="ironing_folding" id="ironing">
                                <label class="form-check-label" for="ironing">Ironing & Folding</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="dry_cleaning" id="drycleaning">
                                <label class="form-check-label" for="drycleaning">Dry Cleaning</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="express_laundry" id="express">
                                <label class="form-check-label" for="express">Express Laundry</label>
                            </div>
                        </div>

                        <div id="gardenerCategories" style="display: none;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="lawn_mowing" id="lawn">
                                <label class="form-check-label" for="lawn">Lawn Mowing</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="plant_care" id="plant">
                                <label class="form-check-label" for="plant">Plant Care & Watering</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="weeding" id="weeding">
                                <label class="form-check-label" for="weeding">Weeding & Pruning</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="garden_cleanup" id="cleanup">
                                <label class="form-check-label" for="cleanup">Garden Cleanup</label>
                            </div>
                        </div>

                        <div id="cleaningCategories" style="display: none;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="house_cleaning" id="house">
                                <label class="form-check-label" for="house">House Cleaning</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="office_cleaning" id="office">
                                <label class="form-check-label" for="office">Office & Commercial Cleaning</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="deep_cleaning" id="deep">
                                <label class="form-check-label" for="deep">Deep Cleaning</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="move_cleaning" id="move">
                                <label class="form-check-label" for="move">Move-In/Move-Out Cleaning</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <textarea id="address" class="form-control" 
                                placeholder="Your address will appear here after getting location" required rows="3" readonly 
                                style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;"></textarea>
                    </div>

                    <button type="button" onclick="getBrowserLocation()" class="btn btn-block mb-4" 
                            style="background: white; color: #1E856D; height: 50px; width: 100%; border-radius: 0.5rem; border: 2px solid #1E856D; font-size: 1rem; font-weight: 500;">
                        📌 Get Location
                    </button>

                    <div id="locationDisplay" class="mb-4" style="color: #666; font-size: 0.9rem;"></div>

                    <button onclick="showRegistrationModal()" id="submitBtn" class="btn btn-block mb-4" disabled
                            style="background: #1E856D; color: white; height: 50px; width: 100%; border-radius: 0.5rem; border: none; font-size: 1rem; font-weight: 500; opacity: 0.6;">
                        Get started
                    </button>

                    <div class="text-center mb-4">
                        <span style="color: #5e5e5e; font-size: 1rem;">Already have an account?</span>
                        <a href="{{ route('provider.login') }}" style="color: #1E856D; text-decoration: none; font-weight: 500; margin-left: 0.5rem;">
                            Sign in
                        </a>
                    </div>

                    <div class="text-center" style="font-size: 0.9rem; color: #666;">
                        By continuing you agree to our 
                        <a href="#" style="color: #1E856D; text-decoration: none;">Terms of Use</a> and
                        <a href="#" style="color: #1E856D; text-decoration: none;">Privacy Policy</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 1rem; border: none;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="font-size: 1.5rem; font-weight: bold; color: #2f3033;">
                    Create your account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-4" style="color: #5e5e5e;">
                    Create an account to get started. Then we'll text you a link to complete your registration.
                    Standard call, messaging, or data rates apply.
                </p>
                
                <form class="form" method="post" action="{{ route('provider.register') }}" id="registerForm">
                    @csrf
                    <input type="hidden" name="service" id="selectedService">
                    <input type="hidden" name="categories" id="selectedCategories">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="address" id="modalAddress">

                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required
                               style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                    </div>

                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Name" required
                               style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                    </div>

                    <div class="mb-3">
                        <input type="text" name="phone" class="form-control" placeholder="Phone" required
                               style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                    </div>

                    <div class="mb-3">
                        <input type="text" name="telegram_username" class="form-control" 
                               placeholder="Telegram Username (e.g. @username)" required
                               style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                    </div>

                    <div class="mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Create a password" required
                               style="height: 50px; background: white; border: 1px solid #ddd; border-radius: 0.5rem; font-size: 1rem; padding: 0.75rem;">
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="soleProprietor" required>
                            <label class="form-check-label" for="soleProprietor" style="color: #5e5e5e;">
                                I acknowledge I am a sole proprietor.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-block mb-3"
                            style="background: #1E856D; color: white; height: 50px; width: 100%; border-radius: 0.5rem; border: none; font-size: 1rem; font-weight: 500;">
                        Create account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: #f7f7f7;
        margin: 0;
        padding: 0;
    }

    .navbar, .navbar-brand, footer, .footer {
        display: none !important;
    }

    .form-control:focus {
        border-color: #1E856D;
        box-shadow: 0 0 0 0.2rem rgba(30, 133, 109, 0.1);
    }

    .btn:hover {
        opacity: 0.9;
    }

    .form-control:disabled, 
    .form-control[readonly] {
        background-color: #f9f9f9;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }
    }
</style>

@push('scripts')
<script>
let selectedService = '';
let selectedCategories = [];

function selectService(service) {
    selectedService = service;
    // Update the hidden service input
    document.getElementById('selectedService').value = service;
    
    // Update dropdown button text
    const emoji = {
        'laundry': '🧺',
        'gardener': '🌿',
        'cleaning': '🧹'
    };
    const serviceName = {
        'laundry': 'Laundry Service',
        'gardener': 'Gardener Service',
        'cleaning': 'Cleaning Service'
    };
    document.getElementById('serviceDropdown').textContent = `${emoji[service]} ${serviceName[service]}`;
    
    // Show categories container
    document.getElementById('categoryContainer').style.display = 'block';
    
    // Hide all category groups
    document.getElementById('laundryCategories').style.display = 'none';
    document.getElementById('gardenerCategories').style.display = 'none';
    document.getElementById('cleaningCategories').style.display = 'none';
    
    // Show selected category group
    document.getElementById(`${service}Categories`).style.display = 'block';
}

// Add event listeners to checkboxes
document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked'))
            .map(cb => cb.value);
        // Update the hidden categories input as JSON
        document.getElementById('selectedCategories').value = JSON.stringify(selectedCategories);
    });
});

// Validate form before submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    if (!selectedService) {
        e.preventDefault();
        alert('Please select a service');
        return false;
    }
    if (selectedCategories.length === 0) {
        e.preventDefault();
        alert('Please select at least one category');
        return false;
    }
});

function getBrowserLocation() {
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                // Set hidden fields
                document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
                
                // Use Reverse Geocoding to get address details
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        // Set the address in the textarea
                        document.getElementById('address').value = data.display_name;
                    
                        // Enable the submit button
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('submitBtn').style.opacity = '1';
                    })
                    .catch(error => {
                        document.getElementById('locationDisplay').innerHTML = `
                            <div class="mt-2">
                                <p>Lat: ${latitude.toFixed(4)}</p>
                                <p>Long: ${longitude.toFixed(4)}</p>
                                <p class="text-warning">Could not get address details</p>
                            </div>
                        `;
                    });
            },
            // Error callback
            function(error) {
                let errorMessage;
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "Please allow location access in your browser settings.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Location information unavailable.";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "Location request timed out.";
                        break;
                    default:
                        errorMessage = "An unknown error occurred.";
                }
                document.getElementById('locationDisplay').innerHTML = errorMessage;
            },
            // Options
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        document.getElementById('locationDisplay').innerHTML = 
            "Geolocation is not supported by this browser.";
    }
}

function showRegistrationModal() {
    // Copy the address to the modal's hidden input
    document.getElementById('modalAddress').value = document.getElementById('address').value;
    
    // Show the modal
    new bootstrap.Modal(document.getElementById('registrationModal')).show();
}
</script>
@endpush