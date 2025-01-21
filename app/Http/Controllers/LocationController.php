<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::create([
            'user_id' => auth()->id(),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location saved successfully',
            'data' => $location
        ]);
    }

    public function getAddress($latitude, $longitude)
    {
        // Optional: Use a geocoding service to get address from coordinates
        // Example using Google Maps Geocoding API
        $apiKey = config('services.google.maps_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        $data = json_decode($response->getBody());
        
        if (!empty($data->results)) {
            return $data->results[0]->formatted_address;
        }
        
        return null;
    }
}
