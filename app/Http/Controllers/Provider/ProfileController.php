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
        \Log::info('Update request received', [
            'has_file' => $request->hasFile('profile_picture'),
            'all_files' => $request->allFiles(),
            'content_type' => $request->header('Content-Type')
        ]);

        $provider = auth()->guard('provider')->user();
        
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'introduction' => 'required|string|max:500',
            'years_experience' => 'required|integer|min:0',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'required|string|max:255',
        ]);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            \Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $file->getPathname(),
                'storage_path_exists' => file_exists(storage_path('app/public')),
                'profiles_path_exists' => file_exists(storage_path('app/public/profiles')),
                'storage_permissions' => decoct(fileperms(storage_path('app/public'))),
            ]);

            try {
                // Create profiles directory if it doesn't exist
                $profilesPath = public_path('storage/profiles');
                if (!file_exists($profilesPath)) {
                    mkdir($profilesPath, 0775, true);
                    \Log::info('Created profiles directory at: ' . $profilesPath);
                }

                // Store with explicit path
                $filename = time() . '_' . $file->getClientOriginalName();
                $result = $file->move($profilesPath, $filename);
                
                if ($result) {
                    $provider->profile_picture = 'profiles/' . $filename;
                    \Log::info('File moved successfully', [
                        'path' => $profilesPath . '/' . $filename,
                        'exists' => file_exists($profilesPath . '/' . $filename)
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Upload failed:', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine()
                ]);
                return back()->with('error', 'Failed to upload: ' . $e->getMessage());
            }
        }

        // Update other fields
        $provider->introduction = $request->introduction;
        $provider->years_experience = $request->years_experience;
        $provider->payment_methods = array_filter($request->payment_methods);

        $provider->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
