@extends('provider.layouts.master')

@section('title', 'Order Management')

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

/* Override sidebar color and text */
.sidebar {
    background: #1E856D !important;
}
.sidebar .nav li a p,
.sidebar .nav li a i,
.sidebar .logo a {
    color: #ffffff !important;
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

/* Table styles */
.table {
    color: #2f3033 !important;
    background-color: #f8f9fa !important;  /* Light grey background */
}

.table thead th {
    border-bottom: 2px solid #e8e8e8;
    color: #000000 !important;
    background-color: #ffffff !important;  /* White background for header */
}

.table tbody tr {
    background-color:rgb(255, 255, 255) !important;  /* Light grey for rows */
}

.table tbody tr:hover {
    background-color: #f2f2f2 !important;  /* Slightly darker grey on hover */
}

.table td {
    border-top: 1px solid #e8e8e8;
    vertical-align: middle;
    color: #000000 !important;
    background-color: transparent !important;
}
.table td:first-child {
    color: #000000 !important;  /* Change this to any color you want */
    
}
.table td:nth-child(2) {
    color: #000000 !important;  /* Change this to any color you want */
}

/* Button styles */
.btn-success {
    background: #1E856D !important;
    border: none;
    color: #ffffff !important;
}

.btn-primary {
    background: #1E856D !important;
    border: none;
    color: #ffffff !important;
}

.btn-info {
    background: #17a2b8 !important;
    border: none;
    color: #ffffff !important;
}

/* Badge styles */
.badge {
    padding: 0.5em 1em;
    color: #ffffff !important;
}

/* Icon colors */
.tim-icons {
    color: inherit;
}

.text-success {
    color: #1E856D !important;
}

/* List items in table */
.list-unstyled li {
    color: #2f3033 !important;
}

/* Make sure all text in table is visible */
td, th {
    color: #2f3033 !important;
}

/* Status badges */
.badge-success {
    background-color: #1E856D !important;
}

.badge-warning {
    background-color: #ff9f43 !important;
}

.badge-danger {
    background-color: #ea5455 !important;
}

.badge-info {
    background-color: #17a2b8 !important;
}

/* Override any remaining white text */
.card-body {
    color: #2f3033 !important;
}

/* Custom text colors for specific table cells */
.table td .date-text {
    color: #000000 !important;  /* For dates */
}

.table td .address-text {
    color: #000000 !important;  /* For address */
}

.table td .amount-text {
    color: #000000 !important;  /* For RM amount */
}

/* Modal styles */
.modal-content {
    background: #ffffff;
    color: #000000;
}

.modal-header {
    border-bottom: 1px solid #e8e8e8;
}

.modal-footer {
    border-top: 1px solid #e8e8e8;
}

.modal-title {
    color: #000000 !important;
}

.modal h6 {
    color: #1E856D !important;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 text-primary font-weight-bold text-dark">Order Management</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td><span class="amount-text">RM {{ number_format($order->total, 2) }}</span></td>
                                    <td>
                                        <span class="badge badge-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <form action="{{ route('provider.orders.update', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="tim-icons icon-check-2"></i> Accept
                                                </button>
                                            </form>
                                        @elseif($order->status === 'processing')
                                            <form action="{{ route('provider.orders.complete', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="tim-icons icon-trophy"></i> Complete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#orderModal{{ $order->id }}">
                                            <i class="tim-icons icon-zoom-split"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for each order -->
@foreach($orders as $order)
<div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Order #{{ $order->id }} Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Service Categories</h6>
                        <ul class="list-unstyled mb-3">
                            @foreach($order->provider->categories as $category)
                                <li><i class="tim-icons icon-check-2 text-success"></i> {{ $category }}</li>
                            @endforeach
                        </ul>
                        <p class="text-muted">
                            Service Cost: RM {{ number_format(count($order->provider->categories) * 10, 2) }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Pickup</h6>
                        <p>{{ $order->pickup_time->format('d M Y') }}<br>
                        <small class="text-muted">{{ $order->pickup_time->format('h:i A') }}</small></p>

                        <h6>Delivery</h6>
                        <p>{{ $order->delivery_time->format('d M Y') }}<br>
                        <small class="text-muted">{{ $order->delivery_time->format('h:i A') }}</small></p>

                        <h6>Address</h6>
                        <p>{{ $order->address }}</p>
                        <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" 
                           target="_blank" 
                           class="btn btn-sm btn-info">
                            <i class="tim-icons icon-map-big"></i> View Map
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
