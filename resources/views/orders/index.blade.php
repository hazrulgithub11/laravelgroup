@extends('admin.layouts.master')

@section('title', 'My Orders')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h1 mb-0 text-primary font-weight-bold">My Orders</h1>
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
                                        @if($order->extra_load_small)
                                            <small class="text-muted">11-20 pieces</small>
                                        @elseif($order->extra_load_large)
                                            <small class="text-muted">20+ pieces</small>
                                        @else
                                            <small class="text-muted">1-10 pieces</small>
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
                                    <td>RM {{ number_format($order->total, 2) }}</td>
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