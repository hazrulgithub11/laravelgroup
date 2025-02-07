<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProviderRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:providers',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'telegram_username' => 'required|string',
            'address' => 'required|string',
            'service' => 'required|in:laundry,gardener,cleaning',
            'categories' => 'required|json',  // Categories will come as JSON string
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $provider = Provider::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'telegram_username' => $request->telegram_username,
                'address' => $request->address,
                'service' => $request->service,
                'categories' => json_decode($request->categories, true),  // Convert JSON string to array
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            // Log the data being saved (for debugging)
            \Log::info('Provider registration data:', [
                'service' => $request->service,
                'categories' => $request->categories
            ]);

            // Log the created provider
            Log::info('Created provider:', $provider->toArray());

            return redirect()->route('provider.login')
                           ->with('success', 'Registration successful! Please login.');

        } catch (\Exception $e) {
            // Log any errors
            \Log::error('Provider registration error:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return back()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
} 