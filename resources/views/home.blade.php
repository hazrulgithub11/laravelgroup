@extends('admin.layouts.master')

@section('title', 'Dashboard')

@push('css')
<style>
/* Override dark theme with white background */
body, 
.wrapper,
.main-panel,
.content {
    background: #ffffff !important;
    color: #2f3033 !important;
}

/* Make Dashboard title black */
.navbar-brand,
.navbar .navbar-brand,
.navbar h4,
.card h4,
.card-title {
    color: #000000 !important;  /* Pure black */
    /* OR */
    /* color: #2f3033 !important; */  /* Soft black */
}

/* Style for the page wrapper */
.page-wrapper {
    background: #ffffff;
    min-height: 100vh;
    padding: 4rem 0;
    position: relative;
}

/* Make all headings black */
h1, h2, h3, h4, h5, h6 {
    color: #000000 !important;
}

/* Style for cards */
.service-card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

/* Override any dark theme text colors */
.text-muted {
    color: #666666 !important;
}

/* Override sidebar color if needed */
.sidebar {
    background: #1E856D !important;
}

/* Override navbar color if needed */
.navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e8e8e8;
}

/* Add these styles to make checkboxes clickable and visible */
.form-check-input {
    cursor: pointer;
    opacity: 1;
    position: static;
    margin-right: 8px;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

/* Style the filter card */
.filters-group {
    margin-bottom: 1rem;
}

.form-check {
    padding-left: 0;
    margin-bottom: 0.5rem;
}

/* Make sure checkboxes are visible and properly sized */
input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    position: relative;
    top: 2px;
}
</style>
@endpush

@section('head')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
<!-- Add a wrapper div for the background -->
<div class="page-wrapper">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="text-center mb-5">
            <h1 class="h1 mb-2 font-weight-bold" style="color: #2f3033;">Book trusted help</h1>
            <p class="text-muted" style="font-size: 1.2rem;">for home tasks</p>
        </div>

        <!-- Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="What do you need help with?" 
                           style="height: 50px; border-radius: 4px 0 0 4px; border: 1px solid #ddd; color: black; background: white;">
                    <div class="input-group-append">
                        <button class="btn" type="button" onclick="handleSearch()"
                                style="background: #1E856D; color: white; padding: 0 1.5rem; border-radius: 0 4px 4px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Replace the existing telegram notification section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                @php
                    $user = auth()->user();
                    $hasTelegramChatId = !empty($user->telegram_chat_id);
                    $isProvider = auth()->user()->provider !== null;
                @endphp

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">📱 Telegram Notifications</h5>

                        @if($hasTelegramChatId)
                            <div class="alert alert-success" style="background: rgba(25, 135, 84, 0.1); border: 1px solid #198754;">
                                <p class="mb-2">✅ Your Telegram is connected!</p>
                                <small class="text-muted">Chat ID: {{ $user->telegram_chat_id }}</small>
                                
                                <div class="mt-3">
                                    <button onclick="testTelegramNotification()" class="btn btn-sm" 
                                            style="background: #1E856D; color: white;">
                                        🔔 Test Notification
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <h6>You will receive notifications for:</h6>
                                <ul class="list-unstyled">
                                    @if(!$isProvider)
                                        <li>✓ Order status updates</li>
                                        <li>✓ Provider assignment</li>
                                        <li>✓ Pickup and delivery reminders</li>
                                    @else
                                        <li>✓ New order notifications</li>
                                        <li>✓ Order assignment alerts</li>
                                        <li>✓ Customer messages</li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107;">
                                <h6 class="text-warning mb-3">⚠️ Telegram Not Connected</h6>
                                
                                @if(!$isProvider)
                                    <p class="mb-2">Connect Telegram to receive:</p>
                                    <ul class="mb-3">
                                        <li>Real-time order updates</li>
                                        <li>Pickup and delivery notifications</li>
                                        <li>Service provider updates</li>
                                    </ul>
                                @else
                                    <p class="mb-2">Connect Telegram to receive:</p>
                                    <ul class="mb-3">
                                        <li>Instant order notifications</li>
                                        <li>Customer messages</li>
                                        <li>Service reminders</li>
                                    </ul>
                                @endif

                                <p class="mb-2">Follow these steps to connect:</p>
                                <ol class="text-left mb-3">
                                    <li>Open Telegram</li>
                                    <li>Search for <a href="https://t.me/LaundrySystem_bot" target="_blank" style="color: #1E856D;">@LaundrySystem_bot</a></li>
                                    <li>Click "Start" or send the /start command</li>
                                    <li>The bot will provide your Chat ID</li>
                                    <li>Update your profile with the provided Chat ID</li>
                                </ol>

                                <a href="{{ route('profile.edit') }}" class="btn btn-sm" 
                                   style="background: #1E856D; color: white;">
                                    Update Profile
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="mb-4">
            <h5 class="mb-4" style="color: #666;">Explore more projects.</h5>
            <div class="row">
                <!-- Laundry Service -->
                <div class="col-md-4 mb-4">
                    <div class="card service-card" onclick="showProviders('laundry')">
                        <img src="/images/washing.jpg" class="card-img-top" alt="Laundry Service"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title mb-0" style="color: black; font-weight: bold;">Laundry Service 🧺</h5>
                        </div>
                    </div>
                </div>

                <!-- Gardening Service -->
                <div class="col-md-4 mb-4">
                    <div class="card service-card" onclick="showProviders('gardening')">
                        <img src="/images/gardening.jpg" class="card-img-top" alt="Gardening Service"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title mb-0" style="color: black; font-weight: bold;">Garden Upkeep 🌿</h5>
                        </div>
                    </div>
                </div>

                <!-- Cleaning Service -->
                <div class="col-md-4 mb-4">
                    <div class="card service-card" onclick="showProviders('cleaning')">
                        <img src="/images/cleaning.jpg" class="card-img-top" alt="Cleaning Service"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title mb-0" style="color: black; font-weight: bold;">Healthy at Home 🧹</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this div for showing providers -->
        <div id="providersSection" class="row mt-4" style="display: none;">
            <!-- Filter Section -->
            <div class="col-12 mb-4">
                <h5 class="mb-3">Filter Services</h5>
                
                <!-- Laundry Categories -->
                <div id="laundryFilters" class="filters-group" style="display: none;">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="filter-option">
                            <input type="checkbox" id="filterWashing" value="washing_drying" class="filter-checkbox">
                            <label for="filterWashing" class="filter-label">Washing & Drying</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterIroning" value="ironing_folding" class="filter-checkbox">
                            <label for="filterIroning" class="filter-label">Ironing & Folding</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterDryCleaning" value="dry_cleaning" class="filter-checkbox">
                            <label for="filterDryCleaning" class="filter-label">Dry Cleaning</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterExpress" value="express_laundry" class="filter-checkbox">
                            <label for="filterExpress" class="filter-label">Express Laundry</label>
                        </div>
                    </div>
                </div>

                <!-- Gardening Categories -->
                <div id="gardeningFilters" class="filters-group" style="display: none;">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="filter-option">
                            <input type="checkbox" id="filterLawn" value="lawn_mowing" class="filter-checkbox">
                            <label for="filterLawn" class="filter-label">Lawn Mowing</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterPlant" value="plant_care" class="filter-checkbox">
                            <label for="filterPlant" class="filter-label">Plant Care & Watering</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterWeeding" value="weeding" class="filter-checkbox">
                            <label for="filterWeeding" class="filter-label">Weeding & Pruning</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterCleanup" value="garden_cleanup" class="filter-checkbox">
                            <label for="filterCleanup" class="filter-label">Garden Cleanup</label>
                        </div>
                    </div>
                </div>

                <!-- Cleaning Categories -->
                <div id="cleaningFilters" class="filters-group" style="display: none;">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="filter-option">
                            <input type="checkbox" id="filterHouse" value="house_cleaning" class="filter-checkbox">
                            <label for="filterHouse" class="filter-label">House Cleaning</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterOffice" value="office_cleaning" class="filter-checkbox">
                            <label for="filterOffice" class="filter-label">Office & Commercial Cleaning</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterDeep" value="deep_cleaning" class="filter-checkbox">
                            <label for="filterDeep" class="filter-label">Deep Cleaning</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="filterMove" value="move_cleaning" class="filter-checkbox">
                            <label for="filterMove" class="filter-label">Move-In/Move-Out Cleaning</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Providers List -->
            <div class="col-12">
                <div id="providersList" class="row">
                    <!-- Providers will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Add background styles */
.page-wrapper {
    background-image: url('/images/background.jpg');  /* Replace with your image path */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    padding: 4rem 0;
    position: relative;
}

/* Add overlay to make content more readable */
.page-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0); /* White overlay with 90% opacity */
    z-index: 0;
}

/* Make sure content stays above overlay */
.container-fluid {
    position: relative;
    z-index: 1;
}

/* Your existing styles */
.service-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 8px;
    overflow: hidden;
    background: white; /* Add white background to cards */
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.card-title {
    color: #2f3033;
    font-weight: 500;
}

.form-control {
    background: white; /* Ensure input has white background */
}

.form-control:focus {
    border-color: #1E856D;
    box-shadow: none;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .h1 {
        font-size: 2rem;
    }

    .page-wrapper {
        padding: 2rem 0;
    }
}

/* Style for filter options */
.filter-option {
    position: relative;
    margin-right: 10px;
    margin-bottom: 10px;
}

.filter-checkbox {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.filter-label {
    display: inline-block;
    padding: 8px 16px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    user-select: none;
    color: #495057;
    transition: all 0.2s ease;
}

.filter-checkbox:checked + .filter-label {
    background-color: #1E856D;
    color: white;
    border-color: #1E856D;
}

.filter-label:hover {
    background-color: #e9ecef;
}

.filter-checkbox:checked + .filter-label:hover {
    background-color: #166c58;
}

.gap-2 {
    gap: 0.5rem;
}

.alert {
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.alert ol {
    margin-bottom: 0;
    padding-left: 1.2rem;
}

.alert ol li {
    margin-bottom: 0.5rem;
}

.alert ol li:last-child {
    margin-bottom: 0;
}

.alert a {
    text-decoration: none;
    font-weight: 500;
}

.alert a:hover {
    text-decoration: underline;
}
</style>

@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const services = {
        'laundry': ['laundry', 'washing', 'clothes', 'dry cleaning', 'ironing'],
        'gardening': ['garden', 'plant', 'lawn', 'gardening', 'landscaping'],
        'cleaning': ['cleaning', 'house cleaning', 'clean', 'housekeeping', 'healthy']
    };

    let foundService = null;
    
    // Check which service matches the search term
    for (const [service, keywords] of Object.entries(services)) {
        if (keywords.some(keyword => searchTerm.includes(keyword))) {
            foundService = service;
            break;
        }
    }

    // Show the corresponding service section
    if (foundService) {
        showProviders(foundService);
        // Scroll to providers section
        document.getElementById('providersSection').scrollIntoView({ behavior: 'smooth' });
    }
}

// Add event listener for Enter key
document.getElementById('searchInput').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        handleSearch();
    }
});


function updateLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by this browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        // Success callback
        position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Log the coordinates to console
            console.log('New location:', {
                latitude: latitude,
                longitude: longitude,
                accuracy: position.coords.accuracy + ' meters',
                timestamp: new Date(position.timestamp).toLocaleString()
            });

            fetch('{{ route('store.location') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    // Update distances without page reload
                    Object.keys(data.distances).forEach(providerId => {
                        const distanceElement = document.getElementById(`distance-${providerId}`);
                        if (distanceElement) {
                            distanceElement.textContent = `${data.distances[providerId]} km away`;
                        }
                    });
                } else {
                    alert('Error updating location');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating location. Please try again.');
            });
        },
        // Error callback
        error => {
            console.error('Geolocation error:', {
                code: error.code,
                message: error.message
            });
            
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

function showProviders(serviceType) {
    const providersSection = document.getElementById('providersSection');
    const providersList = document.getElementById('providersList');
    
    // Show/hide appropriate filter groups
    document.querySelectorAll('.filters-group').forEach(group => {
        group.style.display = 'none';
    });
    const filterGroup = document.getElementById(`${serviceType}Filters`);
    if (filterGroup) {
        filterGroup.style.display = 'block';
    }
    
    providersSection.style.display = 'block';
    providersList.innerHTML = '<div class="col-12 text-center">Loading...</div>';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const userLat = position.coords.latitude;
            const userLong = position.coords.longitude;

            fetch(`/api/providers/${serviceType}?latitude=${userLat}&longitude=${userLong}`)
                .then(response => response.json())
                .then(providers => {
                    // Add this debug log
                    console.log('API Response:', providers);
                    
                    providersList.innerHTML = '';
                    
                    if (!Array.isArray(providers) || providers.length === 0) {
                        providersList.innerHTML = '<div class="col-12 text-center">No providers available for this service.</div>';
                        return;
                    }

                    providers.forEach(provider => {
                        console.log('Raw provider categories:', provider.categories); // Debug log
                        
                        // Parse categories if it's a string
                        let categories = [];
                        if (typeof provider.categories === 'string') {
                            try {
                                categories = JSON.parse(provider.categories);
                            } catch (e) {
                                console.error('Error parsing categories:', e);
                            }
                        } else if (Array.isArray(provider.categories)) {
                            categories = provider.categories;
                        }
                        
                        console.log('Parsed categories:', categories); // Debug log

                        const providerCard = `
                            <div class="col-md-4 mb-4 provider-container">
                                <div class="card provider-card" 
                                     data-categories='${JSON.stringify(categories)}'
                                     style="background-color: white; min-height: 200px; width: 300px; margin: auto;">
                                    <div class="card-body" style="padding: 2rem;">
                                        <h5 class="card-title">${provider.name || 'Unknown Provider'}</h5>
                                        <p class="card-text">
                                            <small class="text-muted">Distance: ${provider.distance}</small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">Services: ${categories.join(', ')}</small>
                                        </p>
                                        <a href="/provider/${provider.id}/profile" 
                                           class="btn btn-dark" 
                                           style="background-color: black; border-color: black; width: 100%;">
                                            View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        providersList.innerHTML += providerCard;
                    });

                    // Initial filter application
                    applyFilters();
                })
                .catch(error => {
                    console.error('Error:', error);
                    providersList.innerHTML = '<div class="col-12 text-center text-danger">Error loading providers. Please try again.</div>';
                });
        },
        function(error) {
            console.error("Error getting location:", error);
            providersList.innerHTML = '<div class="col-12 text-center text-warning">Please enable location services to see provider distances.</div>';
        }
    );
}

function applyFilters() {
    const selectedFilters = Array.from(document.querySelectorAll('.filters-group:not([style*="display: none"]) .filter-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    console.log('Selected filters:', selectedFilters);

    document.querySelectorAll('.provider-container').forEach(container => {
        const card = container.querySelector('.provider-card');
        let providerCategories = [];
        
        try {
            providerCategories = JSON.parse(card.dataset.categories);
            console.log('Provider categories from card:', providerCategories);
        } catch (e) {
            console.error('Error parsing provider categories:', e);
        }

        // Changed the logic here:
        // Show all providers if no filters are selected
        // Otherwise, only show if the provider has ALL selected filters
        const shouldShow = selectedFilters.length === 0 || 
            selectedFilters.every(filter => providerCategories.includes(filter));
        
        console.log('Provider categories:', providerCategories);
        console.log('Should show provider:', shouldShow);
        
        container.style.display = shouldShow ? 'block' : 'none';
    });
}

// Add event listeners when document is ready
document.addEventListener('DOMContentLoaded', function() {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
});

function testTelegramNotification() {
    // Show loading state
    Swal.fire({
        title: 'Sending...',
        text: 'Sending test notification',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("telegram.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Test notification sent! Please check your Telegram.',
                confirmButtonColor: '#1E856D'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to send notification',
                confirmButtonColor: '#1E856D'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to send test notification. Please try again.',
            confirmButtonColor: '#1E856D'
        });
    });
}
</script>
@endpush
