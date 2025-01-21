@extends('layouts.app', ['class' => 'register-page', 'page' => __('Provider Registration'), 'contentClass' => 'register-page'])

@section('content')
<div class="container-fluid" style="width: 100vw; height: 100vh; background-color: #1e1e2f; display: flex; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999;">
    <div class="row justify-content-center" style="width: 80%">
        <div class="col-md-7">
            <div class="card" style="background: #27293d;">
                <div class="card-header text-center py-3" style="background: linear-gradient(to bottom right, #e14eca, #ba54f5);">
                    <h3 class="card-title text-white mb-0">{{ __('Register as Service Provider') }}</h3>
                </div>
                <form class="form" method="post" action="{{ route('provider.register') }}" id="registerForm">
                    @csrf
                    <div class="card-body px-4 py-4">
                        <div class="input-group mb-4">
                            
                            <input type="text" name="name" class="form-control" placeholder="Name" required
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>
                        
                        <div class="input-group mb-4">
                            
                            <input type="email" name="email" class="form-control" placeholder="Email" required
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>
                        
                        <div class="input-group mb-4">
                            
                            <input type="password" name="password" class="form-control" placeholder="Password" required
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>
                        
                        <div class="input-group mb-4">
                            
                            <input type="text" name="phone" class="form-control" placeholder="Phone" required
                                   style="background: #2b3553; border: 1px solid #e14eca; color: white;">
                        </div>

                        <div class="input-group mb-4">
                            <textarea name="address" id="address" class="form-control" placeholder="Your address will appear here after getting location" 
                                      required rows="3" readonly style="background: #2b3553; border: 1px solid #e14eca; color: white;"></textarea>
                        </div>

                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        <button type="button" onclick="getBrowserLocation()" class="btn btn-sm  btn-block mb-4" 
                                style="background: #27293d; color: white; border: 1px solid white;">
                            📌
                        </button>

                        <div id="locationDisplay" class="text-white mb-4"></div>

                        <button type="submit" id="submitBtn" class="btn btn-lg btn-block mb-4" disabled
                                style="background: linear-gradient(to bottom right, #e14eca, #ba54f5); color: white; opacity: 0.6;">
                            {{ __('Register') }}
                        </button>

                        <div class="text-center">
                            <a href="{{ route('provider.login') }}" style="color: #e14eca;">
                                {{ __('Already have an account? Login') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
html, body {
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    height: 100vh !important;
    background: #1e1e2f !important;
    position: fixed !important;
    width: 100% !important;
}

.navbar, .navbar-brand, footer, .footer {
    display: none !important;
}

.container-fluid {
    padding: 0 !important;
    margin: 0 !important;
}

.main-panel, .content {
    margin: 0 !important;
    padding: 0 !important;
    background: #1e1e2f !important;
}

/* Override any template styles */
.wrapper {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    overflow: hidden !important;
    background: #1e1e2f !important;
}

/* Force remove any margins */
* {
    margin-bottom: 0 !important;
}

.form-control {
    height: 45px;
    font-size: 1rem;
}

textarea.form-control {
    height: auto;
}

.input-group-text {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 0;
}

.form-control:focus {
    background: #2b3553;
    border-color: #e14eca;
    color: white;
    box-shadow: none;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.card {
    border: 0;
    border-radius: 0.4rem;
    box-shadow: 0 1px 20px 0px rgba(0, 0, 0, 0.1);
}

.btn {
    height: 45px;
    font-size: 1rem;
    transition: opacity 0.3s ease;
}

.btn:hover {
    opacity: 0.9;
}

.btn:disabled {
    cursor: not-allowed;
}
</style>

@push('scripts')
<script>
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
</script>
@endpush