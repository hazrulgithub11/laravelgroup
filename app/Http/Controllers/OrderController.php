<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Provider;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('provider')
            ->latest()
            ->get();
            
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Make sure the user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function create(Request $request, Order $order = null)
    {
        $providers = Provider::all()->map(function($provider) {
            // If categories is already an array, don't decode it
            $provider->categories = is_string($provider->categories) 
                ? json_decode($provider->categories, true) 
                : ($provider->categories ?? []);
            return $provider;
        });

        // Get the pre-selected provider if provided
        $selectedProviderId = $request->query('provider_id');
        $serviceType = $request->query('service_type');
        
        // Define categories and prices based on service type
        $categories = $this->getServiceCategories($serviceType);
        
        return view('orders.create', compact('providers', 'selectedProviderId', 'order', 'serviceType', 'categories'));
    }

    /**
     * Get service categories and prices based on service type
     */
    private function getServiceCategories($type)
    {
        // Define your service categories and prices
        $categories = [
            'laundry' => [
                ['id' => 'basic', 'name' => 'Basic Wash', 'price' => 30],
                ['id' => 'premium', 'name' => 'Premium Wash', 'price' => 45],
                ['id' => 'deluxe', 'name' => 'Deluxe Package', 'price' => 60],
                ['id' => 'express', 'name' => 'Express Service', 'price' => 75],
            ],
            'garden' => [
                ['id' => 'basic', 'name' => 'Basic Maintenance', 'price' => 50],
                ['id' => 'premium', 'name' => 'Premium Care', 'price' => 80],
                ['id' => 'deluxe', 'name' => 'Full Garden Service', 'price' => 120],
                ['id' => 'express', 'name' => 'Emergency Service', 'price' => 150],
            ],
            'cleaning' => [
                ['id' => 'basic', 'name' => 'Basic Cleaning', 'price' => 40],
                ['id' => 'premium', 'name' => 'Deep Cleaning', 'price' => 70],
                ['id' => 'deluxe', 'name' => 'Full House Service', 'price' => 100],
                ['id' => 'express', 'name' => 'Same Day Service', 'price' => 130],
            ],
        ];
        
        return $categories[$type] ?? [];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'pickup_time' => 'required|date|after:now',
            'delivery_time' => 'required|date|after:pickup_time',
            'delivery_charge' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'provider_id' => $validated['provider_id'],
            'total' => $validated['total'],
            'status' => 'pending',
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'pickup_time' => $validated['pickup_time'],
            'delivery_time' => $validated['delivery_time'],
            'delivery_charge' => $validated['delivery_charge'],
        ]);

        // Send Telegram notification to provider
        try {
            $provider = Provider::findOrFail($validated['provider_id']);
            
            // Check if provider has a telegram chat ID
            if (!$provider->telegram_chat_id) {
                \Log::warning("Provider #{$provider->id} has no Telegram chat ID configured.");
                return redirect()
                    ->route('orders.index')
                    ->with('success', 'Order created successfully!')
                    ->with('warning', 'Provider notification could not be sent (No Telegram chat ID configured).');
            }

            // Send notification using the NewOrderNotification class
            $provider->notify(new NewOrderNotification($order));

            \Log::info('Successfully sent notification to provider', [
                'provider_id' => $provider->id,
                'order_id' => $order->id
            ]);

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order created successfully! Provider has been notified.');

        } catch (\Exception $e) {
            \Log::error('Failed to send provider notification: ' . $e->getMessage());
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order created successfully!')
                ->with('warning', 'Failed to send provider notification: ' . $e->getMessage());
        }
    }

    /**
     * Calculate the distance between two points using the Haversine formula
     *
     * @param float $lat1 First point latitude
     * @param float $lon1 First point longitude
     * @param float $lat2 Second point latitude
     * @param float $lon2 Second point longitude
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return round($earthRadius * $c, 1); // Returns distance in kilometers, rounded to 1 decimal place
    }

    public function edit(Order $order)
    {
        // Make sure the user can only edit their own pending orders
        if ($order->user_id !== auth()->id() || $order->status !== 'pending') {
            abort(403);
        }

        return $this->create(request(), $order);
    }

    public function update(Request $request, Order $order)
    {
        // Make sure the user can only update their own pending orders
        if ($order->user_id !== auth()->id() || $order->status !== 'pending') {
            abort(403);
        }

        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'total' => 'required|numeric|min:0',
            'washing' => 'boolean',
            'ironing' => 'boolean',
            'dry_cleaning' => 'boolean',
            'extra_load' => 'required|in:none,small,large',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'pickup_time' => 'required|date|after:now',
            'delivery_time' => 'required|date|after:pickup_time|after:24_hours_from_pickup',
            'delivery_charge' => 'required|numeric|min:0',
            'telegram_username' => 'required|string|max:255',
        ]);

        // Ensure at least one service is selected
        if (!($validated['washing'] || $validated['ironing'] || $validated['dry_cleaning'])) {
            return back()
                ->withInput()
                ->withErrors(['services' => 'Please select at least one service']);
        }

        $order->update([
            'provider_id' => $validated['provider_id'],
            'washing' => $request->boolean('washing'),
            'ironing' => $request->boolean('ironing'),
            'dry_cleaning' => $request->boolean('dry_cleaning'),
            'extra_load_small' => $request->input('extra_load') === 'small' ? 1 : 0,
            'extra_load_large' => $request->input('extra_load') === 'large' ? 1 : 0,
            'total' => $validated['total'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'pickup_time' => $validated['pickup_time'],
            'delivery_time' => $validated['delivery_time'],
            'delivery_charge' => $validated['delivery_charge'],
            'telegram_username' => $validated['telegram_username'],
        ]);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        // Make sure the user can only delete their own pending orders
        if ($order->user_id !== auth()->id() || $order->status !== 'pending') {
            abort(403);
        }

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order cancelled successfully!');
    }
} 