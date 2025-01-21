<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="{{ url('/') }}" class="simple-text logo-mini">
                LS
            </a>
            <a href="{{ url('/') }}" class="simple-text logo-normal">
                Laundry System
            </a>
        </div>
        <ul class="nav">
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-delivery-fast"></i>
                    <p>My Orders</p>
                </a>
            </li>
            <li class="{{ Request::is('Favorites') ? 'active' : '' }}">
                <a href="#">
                    <i class="tim-icons icon-pin"></i>
                    <p>Favorites</p>
                </a>
            </li>
            <li class="{{ Request::is('Logout') ? 'active' : '' }}">
                <a href="#">
                    <i class="tim-icons icon-tag"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- @push('scripts')
<script>
function getBrowserLocation() {
    document.getElementById('locationDisplay').innerHTML = 'Getting accurate location...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                // Use Reverse Geocoding to get address details
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        document.getElementById('locationDisplay').innerHTML = `
                            <div class="mt-2">
                                <p>City: ${address.city || address.town || address.village || 'N/A'}</p>
                                <p>State: ${address.state || 'N/A'}</p>
                                <p>Country: ${address.country || 'N/A'}</p>
                                <p>Lat: ${latitude.toFixed(4)}</p>
                                <p>Long: ${longitude.toFixed(4)}</p>
                            </div>
                        `;
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
@endpush  -->