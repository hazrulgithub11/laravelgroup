<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:provider');
    }

    public function index()
    {
        // Get all orders for the logged-in provider
        $orders = Order::where('provider_id', auth()->guard('provider')->id())
            ->with('user')  // Eager load the user relationship
            ->latest()      // Most recent orders first
            ->get();
        
        return view('provider.dashboard', compact('orders'));
    }

    public function updateOrderStatus(Order $order)
    {
        // Verify the order belongs to this provider
        if ($order->provider_id !== auth()->guard('provider')->id()) {
            abort(403);
        }

        // Update from pending to processing
        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
            return back()->with('success', 'Order accepted successfully!');
        }

        return back()->with('error', 'Invalid order status update');
    }

    public function completeOrder(Order $order)
    {
        // Verify the order belongs to this provider
        if ($order->provider_id !== auth()->guard('provider')->id()) {
            abort(403);
        }

        // Update from processing to completed
        if ($order->status === 'processing') {
            $order->update(['status' => 'completed']);
            return back()->with('success', 'Order marked as completed!');
        }

        return back()->with('error', 'Invalid order status update');
    }
} 