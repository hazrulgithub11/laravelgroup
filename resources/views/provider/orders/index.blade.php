@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Management</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ORDER ID</th>
                                    <th>CUSTOMER</th>
                                    <th>TOTAL</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                    <th>DETAILS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr id="order-row-{{ $order->id }}">
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>RM {{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $order->status === 'pending' ? 'warning' : 'info' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <button 
                                                class="btn btn-success btn-sm accept-order" 
                                                data-order-id="{{ $order->id }}"
                                                onclick="acceptOrder({{ $order->id }})">
                                                <i class="tim-icons icon-check-2"></i> Accept
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm">
                                            <i class="tim-icons icon-zoom-split"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
function acceptOrder(orderId) {
    $.ajax({
        url: `/provider/orders/${orderId}/update-status`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update the status badge
                $(`#order-row-${orderId} td:nth-child(4)`).html(
                    `<span class="badge badge-info">Processing</span>`
                );
                
                // Remove the accept button
                $(`#order-row-${orderId} td:nth-child(5)`).html('');
                
                // Show success message
                toastr.success('Order accepted successfully');
            }
        },
        error: function(xhr) {
            toastr.error('Failed to update order status');
        }
    });
}

// Auto refresh the page every 30 seconds
setTimeout(function() {
    window.location.reload();
}, 30000);
</script>
@endpush

@push('head')
    <meta http-equiv="refresh" content="5">  {{-- Refresh every 5 seconds --}}
@endpush 