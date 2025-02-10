@extends('admin.layouts.master')

@section('title', 'My Orders')

@push('css')
<style>
/* Override theme colors */
body, .wrapper, .main-panel, .content {
    background: #ffffff !important;
    color: #2f3033 !important;
}

.card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

/* Table styles */
.table {
    color: #2f3033 !important;
    background-color: #ffffff !important;
}

.table thead th {
    border-bottom: 2px solid #e8e8e8;
    color: #000000 !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.table tbody tr {
    border-bottom: 1px solid #e8e8e8;
}

.table td {
    vertical-align: middle;
    color: #2f3033 !important;
}

.table td:first-child {
    color: #000000 !important;  /* Black color for Order ID */
    font-weight: 500;  /* Make it slightly bold */
}

.table td:nth-child(2) {
    color: #000000 !important;  /* Black color for Provider Name */
    font-weight: 500;  /* Make it slightly bold */
}

.table td:nth-child(4),
.table td:nth-child(5) {
    color: #000000 !important;  /* Black color for dates */
    font-weight: 500;  /* Make it slightly bold */
}

.table td:nth-child(4) small,
.table td:nth-child(5) small {
    color: #6c757d !important;  /* Grey color for times */
    font-weight: normal;  /* Normal weight for small text */
}

.table td:nth-child(6) {
    color: #000000 !important;  /* Black color for delivery charge */
    font-weight: 500;  /* Make it slightly bold */
}

.table td:nth-child(6) small {
    color: #6c757d !important;  /* Grey color for distance */
    font-weight: normal;  /* Normal weight for small text */
}



.table .list-unstyled li {
    color: #000000 !important;  /* Black color for category items */
    font-weight: 500;  /* Make it slightly bold */
}

.table .list-unstyled li i {
    color: #1E856D !important;  /* Green color for the check icon */
    margin-right: 5px;  /* Add some space between icon and text */
}


/* Button styles */
.btn-info {
    background: #17a2b8 !important;
    border: none;
}

.btn-success {
    background: #1E856D !important;
    border: none;
}

.btn-warning {
    background: #ff9f43 !important;
    border: none;
}

.btn-danger {
    background: #ea5455 !important;
    border: none;
}

/* Badge styles */
.badge {
    padding: 0.5em 1em;
    border-radius: 4px;
}

.badge-success {
    background-color: #1E856D !important;
}

.badge-warning {
    background-color: #ff9f43 !important;
}

.badge-info {
    background-color: #17a2b8 !important;
}

.badge-danger {
    background-color: #ea5455 !important;
}

/* Text colors */
.text-muted {
    color: #6c757d !important;
}

.text-success {
    color: #1E856D !important;
}

/* Modal styles */
.modal-content {
    background: #ffffff;
    color: #2f3033;
}

.modal-header {
    border-bottom: 1px solid #e8e8e8;
}

.modal-footer {
    border-top: 1px solid #e8e8e8;
}

/* Add this to your CSS styles */
.wrapper, .main-panel, .sidebar {
    background:rgb(253, 253, 253) !important;  /* Green color matching your theme */
}

.sidebar .nav li > a {
    color: #ffffff !important;
}

.sidebar .nav li.active > a,
.sidebar .nav li > a:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

.sidebar .sidebar-wrapper {
    background: #1E856D !important;
}

.sidebar .logo {
    background: #1E856D !important;
}

.sidebar .logo a {
    color: #ffffff !important;
}

.sidebar .simple-text {
    color: #ffffff !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 font-weight-bold" style="color:rgb(0, 0, 0);">My Orders</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order History</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Provider</th>
                                    <th>Services</th>
                                    <th>Pickup Date</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->provider->name }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($order->provider->categories as $category)
                                                <li><i class="tim-icons icon-check-2 text-success"></i> {{ $category }}</li>
                                            @endforeach
                                        </ul>
                                        <small class="text-muted">
                                            Total Categories: {{ count($order->provider->categories) }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $order->pickup_time->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $order->pickup_time->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ $order->delivery_time->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $order->delivery_time->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        RM {{ number_format($order->delivery_charge, 2) }}<br>
                                        <small class="text-muted">
                                            @if(isset($order->provider->distance))
                                                ({{ $order->provider->distance }} km)
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td><span style="color: rgb(0, 0, 0);">RM {{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('orders.edit', $order->id) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="tim-icons icon-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('orders.destroy', $order->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="tim-icons icon-simple-remove"></i> Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">No actions available</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        No orders found
                                    </td>
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
@endsection 