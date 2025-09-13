<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'fcm_token' => ['nullable', 'string'], // FCM token validation
        ], [
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a valid string.',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        // Check if user exists
        $user = \App\Models\User::where('email', $request->email)->first();
       
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided email is not registered.'],
            ]);
        }
       
        // Attempt login with remember me always set to true
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
            $request->session()->regenerate();
            
            // FCM token save করুন login success এর সময়
            if ($request->filled('fcm_token')) {
                $user->fcm_token = $request->fcm_token;
                $user->save();
            }
           
            // User identifier তৈরি করুন
            $userIdentifier = $user->username ?? str_replace(['@', '.', '+', '-', ' '], '', $user->email);
           
            // Laravel basic path দিয়ে redirect
            return redirect("/login-success/{$userIdentifier}");
        }
       
        // If we get here, password is wrong (since user exists)
        throw ValidationException::withMessages([
            'password' => ['The provided password is incorrect.'],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Current user এর identifier নিন logout করার আগে
        $user = Auth::user();
        $userIdentifier = $user->username ?? str_replace(['@', '.', '+', '-', ' '], '', $user->email);
        
        // FCM token remove করুন logout এর সময়
        if ($user) {
            $user->fcm_token = null;
            $user->save();
        }
       
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
       
        // Laravel basic path দিয়ে redirect
        return redirect("/logout-success/{$userIdentifier}");
    }

    /**
     * Save FCM token after successful login
     */
    public function saveFcmTokenOnLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if there's a stored FCM token in the request or session
            $fcmToken = $request->input('fcm_token') ?? session('temp_fcm_token');
            
            if ($fcmToken) {
                $user->fcm_token = $fcmToken;
                $user->save();
                
                // Clear temporary token from session
                session()->forget('temp_fcm_token');
                
                return response()->json([
                    'success' => true,
                    'message' => 'FCM token saved on login'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No token to save or user not authenticated'
        ]);
    }
}