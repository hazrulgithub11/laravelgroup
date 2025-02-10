<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Notifications\OrderAcceptedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
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
                      
        return view('provider.orders.index', compact('orders'));
    }

    public function updateStatus(Order $order)
    {
        try {
            DB::beginTransaction();
            
            if ($order->provider_id !== auth()->guard('provider')->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($order->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Invalid order status']);
            }

            $order->status = 'processing';
            $order->save();

            // Notify user
            $order->user->notify(new OrderAcceptedNotification($order));

            DB::commit();

            Log::info('Order status updated via web:', [
                'order_id' => $order->id,
                'new_status' => 'processing',
                'provider_id' => auth()->guard('provider')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order accepted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status:', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status'
            ], 500);
        }
    }
} 