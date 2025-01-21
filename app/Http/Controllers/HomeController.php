<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $providers = Provider::all();
        
        // Get user's location from session
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');

        if ($userLat && $userLng) {
            foreach ($providers as $provider) {
                $provider->distance = $this->calculateDistance(
                    $userLat,
                    $userLng,
                    $provider->latitude,
                    $provider->longitude
                );
            }
        }

        return view('home', compact('providers'));
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        session([
            'user_latitude' => $request->latitude,
            'user_longitude' => $request->longitude
        ]);

        return response()->json(['success' => true]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return round($earthRadius * $c, 1);
    }
}
