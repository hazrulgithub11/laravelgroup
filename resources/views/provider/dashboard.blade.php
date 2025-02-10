@extends('provider.layouts.master')

@section('title', 'Provider Dashboard')

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
    color: #000000 !important;
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

/* Override any dark theme text colors */
.text-muted {
    color: #666666 !important;
}

/* Override sidebar color */
.sidebar {
    background: #1E856D !important;
}

/* Override navbar color */
.navbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e8e8e8;
}

/* Style for cards */
.card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

/* Override button styles */
.btn-danger {
    background: #ff5b5b !important;
    border: none;
}

.btn-danger:hover {
    background: #ff3333 !important;
}

/* Make sure icons are visible */
.tim-icons {
    color: inherit;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Telegram Notification Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            @php
                $provider = auth()->guard('provider')->user();
                $hasTelegramChatId = !empty($provider->telegram_chat_id);
            @endphp

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">📱 Telegram Notifications</h5>

                    @if($hasTelegramChatId)
                        <div class="alert alert-success" style="background: rgba(25, 135, 84, 0.1); border: 1px solid #198754;">
                            <p class="mb-2">✅ Your Telegram is connected!</p>
                            <small class="text-muted">Chat ID: {{ $provider->telegram_chat_id }}</small>
                            
                            <div class="mt-3">
                                <button onclick="testTelegramNotification()" class="btn btn-sm" 
                                        style="background: #1E856D; color: #ffffff;">
                                    🔔 Test Notification
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6>You will receive notifications for:</h6>
                            <ul class="list-unstyled">
                                <li>✓ New order notifications</li>
                                <li>✓ Order assignment alerts</li>
                                <li>✓ Customer messages</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107;">
                            <h6 class="text-warning mb-3">⚠️ Telegram Not Connected</h6>
                            
                            <p class="mb-2">Connect Telegram to receive:</p>
                            <ul class="mb-3">
                                <li>Instant order notifications</li>
                                <li>Customer messages</li>
                                <li>Service reminders</li>
                            </ul>

                            <p class="mb-2">Follow these steps to connect:</p>
                            <ol class="text-left mb-3">
                                <li>Open Telegram</li>
                                <li>Search for <a href="https://t.me/LaundrySystem_bot" target="_blank" style="color: #1E856D;">@LaundrySystem_bot</a></li>
                                <li>Click "Start" or send the /start command</li>
                                <li>The bot will provide your Chat ID</li>
                                <li>Update your profile with the provided Chat ID</li>
                            </ol>

                            <a href="{{ route('provider.profile.edit') }}" class="btn btn-sm" 
                               style="background: #1E856D; color: #ffffff;">
                                Update Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 font-weight-bold" style="color: #2f3033;">Provider Dashboard</h1>
        <form method="POST" action="{{ route('provider.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="tim-icons icon-button-power"></i>
                {{ __('Logout') }}
            </button>
        </form>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        @forelse($orders as $order)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0" style="color: #000000;">Order #{{ $order->id }}</h5>
                            <span class="badge badge-{{ $order->status_color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="customer-info">
                            <p class="mb-2" style="color: #000000;">
                                <i class="tim-icons icon-single-02"></i>
                                <strong>Customer:</strong> {{ $order->user->name }}
                            </p>
                            <p class="mb-2" style="color: #000000;">
                                <i class="tim-icons icon-coins"></i>
                                <strong>Total:</strong> RM {{ number_format($order->total, 2) }}
                            </p>
                            <p class="mb-2" style="color: #000000;">
                                <i class="tim-icons icon-delivery-fast"></i>
                                <strong>Services:</strong>
                                @if($order->washing) Washing @endif
                                @if($order->ironing) Ironing @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-0" style="color: #000000;">No orders found</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function testTelegramNotification() {
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
    .then(response => response.json())
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