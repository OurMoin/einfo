<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Country;
use App\Models\City;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $countries = Country::all();
        $cities = [];
       
        // If user has a country selected, get cities for that country
        if ($request->user()->country_id) {
            $cities = City::where('country_id', $request->user()->country_id)->get();
        }
       
        return view('profile.edit', [
            'user' => $request->user(),
            'countries' => $countries,
            'cities' => $cities,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
       
        // Validate and get the data
        $validated = $request->validated();
       
        // Handle image upload - FIXED to match registration format
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                // Handle both old and new image path formats
                $oldImagePath = public_path('profile-image/' . $user->image);
                $newImagePath = storage_path('app/public/profile-images/' . $user->image);
               
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                if (file_exists($newImagePath)) {
                    unlink($newImagePath);
                }
            }
           
            // Store new image using the same method as registration
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('profile-image'), $imageName);
            $validated['image'] = $imageName;
        }
       
        // Check if email is being changed and send OTP
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $newEmail = $validated['email'];
            
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Add OTP to the data that will be saved
            $validated['otp'] = $otp;
            
            // Send OTP to new email
            Mail::raw("Your OTP code for email verification is: $otp", function($message) use ($newEmail) {
                $message->to($newEmail)
                        ->subject('Email Update Verification - Wihima');
            });
            
            // Reset email verification since email changed
            $validated['email_verified_at'] = null;
        }
       
        // Update user data
        $user->fill($validated);
        $user->save();
       
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete user's profile image if exists
        if ($user->image) {
            $oldImagePath = public_path('profile-image/' . $user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}