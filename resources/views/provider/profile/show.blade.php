@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $provider->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <!-- Provider Header -->
            <div class="d-flex align-items-center mb-4">
                @if($provider->profile_picture)
                    <img src="{{ asset('storage/' . $provider->profile_picture) }}" 
                         alt="{{ $provider->name }}" 
                         class="rounded-circle"
                         style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                @endif
                
                <div class="ms-3">
                    <h1 class="h3 mb-1">{{ $provider->name }}</h1>
                    <div class="d-flex align-items-center">
                        <span class="text-success me-2">Very good {{ number_format($averageRating, 1) }}</span>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $averageRating)
                                <i class="fas fa-star text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                        <span class="ms-1">({{ $totalReviews }})</span>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="#about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reviews">Reviews</a>
                </li>
            </ul>

            <!-- About Section -->
            <div class="mb-5">
                <h2 class="h4 mb-3">Introduction</h2>
                <p>{{ $provider->introduction }}</p>
            </div>

            <!-- Overview Section -->
            <div class="mb-5">
                <h2 class="h4 mb-3">Overview</h2>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Experience: {{ $provider->years_experience }} years
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                Serves: {{ $provider->service_area ?? 'Local Area' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                Member since: {{ $provider->created_at->format('F Y') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="mb-5">
                <h2 class="h4 mb-3">Payment Methods</h2>
                <ul class="list-unstyled">
                    @foreach($provider->payment_methods ?? [] as $method)
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            {{ $method }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Booking Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title h5 mb-3">Book {{ $provider->name }}</h3>
                    <a href="{{ route('orders.create', ['provider' => $provider->id]) }}" 
                       class="btn btn-primary w-100">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
</style>
@endpush
