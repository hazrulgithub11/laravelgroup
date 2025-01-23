@extends('provider.layouts.master')

@section('title', 'Provider Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 text-primary font-weight-bold">Provider Dashboard</h1>
        <form method="POST" action="{{ route('provider.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="tim-icons icon-button-power"></i>
                {{ __('Logout') }}
            </button>
        </form>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Management</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Services</th>
                                    <th>Load Size</th>
                                    <th>Pickup</th>
                                    <th>Delivery</th>
                                    <th>Address</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @if($order->washing)
                                                <li><i class="tim-icons icon-check-2 text-success"></i> Washing</li>
                                            @endif
                                            @if($order->ironing)
                                                <li><i class="tim-icons icon-check-2 text-success"></i> Ironing</li>
                                            @endif
                                            @if($order->dry_cleaning)
                                                <li><i class="tim-icons icon-check-2 text-success"></i> Dry Cleaning</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        @if($order->extra_load_small)
                                            <span class="text-muted">11-20 pieces</span>
                                        @elseif($order->extra_load_large)
                                            <span class="text-muted">20+ pieces</span>
                                        @else
                                            <span class="text-muted">1-10 pieces</span>
                                        @endif
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
                                        {{ $order->address }}<br>
                                        <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-info">
                                            <i class="tim-icons icon-map-big"></i> View Map
                                        </a>
                                    </td>
                                    <td>RM {{ number_format($order->total, 2) }}</td>
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
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success"
                                                        onclick="return confirm('Are you sure you want to accept this order?')">
                                                    <i class="tim-icons icon-check-2"></i> Accept
                                                </button>
                                            </form>
                                        @elseif($order->status === 'processing')
                                            <form action="{{ route('provider.orders.complete', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-primary"
                                                        onclick="return confirm('Mark this order as completed?')">
                                                    <i class="tim-icons icon-trophy"></i> Complete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
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