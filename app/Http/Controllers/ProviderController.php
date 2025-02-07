<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Provider;

class ProviderController extends Controller
{
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Earth's radius in kilometers
        $r = 6371;
        
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        $a = sin($dLat/2) * sin($dLat/2) + cos($lat1) * cos($lat2) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $r * $c;
        
        return round($distance, 1); // Return distance in km with 1 decimal place
    }

    public function getByService(Request $request, $service)
    {
        try {
            // Get user's coordinates from request
            $userLat = $request->query('latitude');
            $userLong = $request->query('longitude');

            // Map the frontend service names to database values
            $serviceMap = [
                'gardening' => 'gardener',
                'laundry' => 'laundry',
                'cleaning' => 'cleaning'
            ];

            // Get the correct service name from the map
            $dbService = $serviceMap[$service] ?? $service;

            // Get providers from the providers table
            $providers = DB::table('providers')
                ->where('service', $dbService)  // Changed from LIKE to exact match with mapped service
                ->select('id', 'name', 'latitude', 'longitude', 'categories')
                ->get()
                ->map(function($provider) use ($userLat, $userLong) {
                    $distance = 'Distance not available';
                    
                    if ($userLat && $userLong && $provider->latitude && $provider->longitude) {
                        $distance = $this->calculateDistance(
                            $userLat,
                            $userLong,
                            $provider->latitude,
                            $provider->longitude
                        );
                    }

                    // Parse categories from JSON string if needed
                    $categories = is_string($provider->categories) 
                        ? json_decode($provider->categories, true) 
                        : $provider->categories;

                    return [
                        'id' => $provider->id,
                        'name' => $provider->name,
                        'distance' => $distance . ' km',
                        'categories' => $categories
                    ];
                });

            return response()->json($providers);
        } catch (\Exception $e) {
            \Log::error('Provider fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function showProfile($id)
    {
        $provider = Provider::findOrFail($id);
        
        // Initialize default values
        $averageRating = 0;
        $totalReviews = 0;

        // Check if the reviews relationship exists before accessing it
        if ($provider->reviews()->exists()) {
            $averageRating = $provider->reviews()->avg('rating') ?? 0;
            $totalReviews = $provider->reviews()->count() ?? 0;
        }
        
        return view('provider.profile.show', compact('provider', 'averageRating', 'totalReviews'));
    }
} 