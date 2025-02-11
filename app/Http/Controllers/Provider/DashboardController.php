<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Notifications\OrderAcceptedNotification;
use App\Notifications\OrderCancelledNotification;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:provider');
    }

    public function index()
    {
        $orders = Order::where('provider_id', auth()->guard('provider')->id())
            ->with('user')
            ->latest()
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
            
            // Send Telegram notification to user
            try {
                $order->user->notify(new OrderAcceptedNotification($order));
                return back()->with('success', 'Order accepted and customer notified successfully!');
            } catch (\Exception $e) {
                // Order updated but notification failed
                return back()->with([
                    'success' => 'Order accepted successfully!',
                    'warning' => 'Could not send notification to customer.'
                ]);
            }
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

    public function profile()
    {
        return view('provider.profile');
    }

    public function updateProfile(Request $request)
    {
        return redirect()->route('provider.profile.edit');
    }

    public function orders()
    {
        // Get all orders for the logged-in provider
        $orders = Order::where('provider_id', auth()->guard('provider')->id())
            ->with('user')  // Eager load the user relationship
            ->latest()      // Most recent orders first
            ->get();
        
        return view('provider.orders', compact('orders'));
    }

    public function acceptOrder(Order $order)
    {
        // Verify the order belongs to this provider
        if ($order->provider_id !== auth()->guard('provider')->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Order cannot be accepted - invalid status');
        }

        $order->update(['status' => 'processing']);
        
        // Send notification to user
        try {
            $order->user->notify(new OrderAcceptedNotification($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send user notification: ' . $e->getMessage());
        }

        return redirect()->route('provider.dashboard')
            ->with('success', 'Order accepted successfully!');
    }

    public function cancelOrder(Order $order)
    {
        // Verify the order belongs to this provider
        if ($order->provider_id !== auth()->guard('provider')->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Order cannot be cancelled - invalid status');
        }

        $order->update(['status' => 'cancelled']);
        
        // Notify user about cancellation
        try {
            $order->user->notify(new OrderCancelledNotification($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send cancellation notification: ' . $e->getMessage());
        }

        return redirect()->route('provider.dashboard')
            ->with('success', 'Order cancelled successfully');
    }
} 