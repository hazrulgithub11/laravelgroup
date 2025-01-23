@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 text-primary font-weight-bold">ORDER NOW</h1>
        <button onclick="updateLocation()" class="btn btn-primary">
            <i class="tim-icons icon-pin"></i> Update My Location
        </button>
    </div>

    <!-- Content Row -->
    <div class="row">
        @foreach($providers as $provider)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h2 font-weight-bold text-success text-uppercase mb-5">
                                {{ $provider->name }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold" style="color: yellow">🌟🌟🌟🌟🌟</div>
                            <div class="h3 mb-0 font-weight-bold" style="color: #fcd53f">
                                <span id="distance-{{ $provider->id }}">
                                    @if(session('user_latitude') && session('user_longitude'))
                                        {{ $provider->distance }} km away
                                    @else
                                        <span class="text-warning">Click Update Location to see distance</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
