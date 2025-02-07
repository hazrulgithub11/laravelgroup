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
        // Get all providers with distances if user location is available
        $providers = Provider::all();
        
        if (session('user_latitude') && session('user_longitude')) {
            foreach ($providers as $provider) {
                $provider->distance = $this->calculateDistance(
                    session('user_latitude'),
                    session('user_longitude'),
                    $provider->latitude,
                    $provider->longitude
                );
            }
            // Sort providers by distance
            $providers = $providers->sortBy('distance');
        }
        
        // Get the pre-selected provider if provided
        $selectedProviderId = $request->query('provider_id');
        
        return view('orders.create', compact('providers', 'selectedProviderId', 'order'));
    }

    public function store(Request $request)
    {
        // Add validation rules for new fields
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
        ], [
            'delivery_time.after' => 'Delivery time must be at least 24 hours after pickup time'
        ]);
        
        // Ensure at least one service is selected
        if (!($validated['washing'] || $validated['ironing'] || $validated['dry_cleaning'])) {
            return back()
                ->withInput()
                ->withErrors(['services' => 'Please select at least one service']);
        }

        // Create the order with additional fields
        $order = Order::create([
            'user_id' => auth()->id(),
            'provider_id' => $validated['provider_id'],
            'washing' => $request->boolean('washing'),
            'ironing' => $request->boolean('ironing'),
            'dry_cleaning' => $request->boolean('dry_cleaning'),
            'extra_load_small' => $request->input('extra_load') === 'small' ? 1 : 0,
            'extra_load_large' => $request->input('extra_load') === 'large' ? 1 : 0,
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
            
            // Debug log to see provider details
            \Log::info('Attempting to send notification to provider:', [
                'provider_id' => $provider->id,
                'telegram_chat_id' => $provider->telegram_chat_id
            ]);
            
            // Check if provider has a telegram chat ID
            if (!$provider->telegram_chat_id) {
                \Log::warning("Provider #{$provider->id} has no Telegram chat ID configured.");
                return redirect()
                    ->route('orders.index')
                    ->with('success', 'Order created successfully!')
                    ->with('warning', 'Provider notification could not be sent (No Telegram chat ID configured).');
            }

            // Try to send notification
            try {
                // Send direct Telegram message
                $response = Http::post('https://api.telegram.org/bot' . config('services.telegram-bot-api.token') . '/sendMessage', [
                    'chat_id' => $provider->telegram_chat_id,
                    'text' => "🔔 *New Order #" . $order->id . "*\n\n" .
                             "📦 Services: " . implode(', ', array_filter([
                                 $order->washing ? 'Washing' : null,
                                 $order->ironing ? 'Ironing' : null,
                                 $order->dry_cleaning ? 'Dry Cleaning' : null
                             ])) . "\n" .
                             "📅 Pickup: " . $order->pickup_time->format('d M Y h:i A') . "\n" .
                             "🚚 Delivery: " . $order->delivery_time->format('d M Y h:i A') . "\n" .
                             "💰 Total: RM " . number_format($order->total, 2) . "\n" .
                             "📍 Address: " . $order->address . "\n\n" .
                             "Please login to your dashboard to accept this order.",
                    'parse_mode' => 'Markdown'
                ]);

                if ($response->successful() && ($response->json()['ok'] ?? false)) {
                    \Log::info('Successfully sent notification to provider', [
                        'provider_id' => $provider->id,
                        'order_id' => $order->id
                    ]);
                    return redirect()
                        ->route('orders.index')
                        ->with('success', 'Order created successfully! Provider has been notified.');
                } else {
                    throw new \Exception($response->body());
                }
            } catch (\Exception $e) {
                // Log the specific Telegram error
                \Log::error('Telegram notification failed:', [
                    'error' => $e->getMessage(),
                    'provider_id' => $provider->id,
                    'telegram_chat_id' => $provider->telegram_chat_id
                ]);
                return redirect()
                    ->route('orders.index')
                    ->with('success', 'Order created successfully!')
                    ->with('warning', "Couldn't send notification to provider. They will see the order in their dashboard.");
            }
        } catch (\Exception $e) {
            // Log any other errors but don't stop the order creation
            \Log::error('Failed to send Telegram notification:', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order created successfully!')
                ->with('warning', 'Could not send notification to provider.');
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