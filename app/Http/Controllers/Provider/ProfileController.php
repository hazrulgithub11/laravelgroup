<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $provider = Auth::guard('provider')->user();
        return view('provider.profile.edit', compact('provider'));
    }

    public function update(Request $request)
    {
        $provider = Auth::guard('provider')->user();
        
        $request->validate([
            'profile_picture' => 'nullable|image|max:2048',
            'introduction' => 'required|string|max:1000',
            'years_experience' => 'required|integer|min:0',
            'payment_methods' => 'required|array',
            'payment_methods.*' => 'required|string'
        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                
                // Debug file information
                \Log::info('Uploading file:', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'storage_path' => storage_path('app/public/profiles'),
                    'public_path' => public_path('storage/profiles')
                ]);

                // Create a unique filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Ensure directory exists
                if (!file_exists(storage_path('app/public/profiles'))) {
                    \Log::info('Creating directory: ' . storage_path('app/public/profiles'));
                    mkdir(storage_path('app/public/profiles'), 0775, true);
                }

                // Try to move the file
                try {
                    $file->move(storage_path('app/public/profiles'), $filename);
                    \Log::info('File moved successfully to: ' . storage_path('app/public/profiles/' . $filename));
                    
                    // Update database with the path
                    $provider->profile_picture = 'profiles/' . $filename;
                    \Log::info('Database path set to: ' . $provider->profile_picture);
                } catch (\Exception $e) {
                    \Log::error('File move failed: ' . $e->getMessage());
                    throw $e;
                }
            }

            // Update other fields
            $provider->introduction = $request->introduction;
            $provider->years_experience = $request->years_experience;
            
            // Filter out empty payment methods
            $paymentMethods = array_values(array_filter($request->payment_methods, function($method) {
                return !empty(trim($method));
            }));
            $provider->payment_methods = $paymentMethods;
            
            // Save and log the result
            $saved = $provider->save();
            \Log::info('Provider save result: ' . ($saved ? 'success' : 'failed'), [
                'profile_picture' => $provider->profile_picture,
                'introduction' => $provider->introduction,
                'years_experience' => $provider->years_experience
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
