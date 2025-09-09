<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\User;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Category;

class LocationController extends Controller
{
    public function show($username)
    {
        // ইউজার খুঁজে বের করো
        $user = User::where('username', $username)->first();
        
        // যদি ইউজার না পাওয়া যায় → redirect to /
        if (!$user) {
            return redirect('/');
        }
        
        // ওই ইউজারের সব পোস্ট নাও - pagination সহ (category relationship সহ)
        $posts = Post::with(['user', 'category'])
                     ->where('user_id', $user->id)
                     ->latest()
                     ->paginate(3); // get() এর পরিবর্তে paginate() ব্যবহার করুন
        
        // Categories fetch করা (form এর জন্য - শুধুমাত্র নিজের প্রোফাইলে দেখাবে)
        $categories = \App\Models\Category::whereIn('cat_type', ['product', 'service'])->get();
        
        // view এ পাঠাও
        return view("dashboard", compact('posts', 'user', 'categories'));
    }

    // User-specific posts এর জন্য AJAX load more method
    public function loadMoreUserPosts(Request $request, $userId)
    {
        $posts = Post::with(['user', 'category'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(3); // প্রতি page এ 3টি post
        
        // যদি AJAX request হয় (lazy loading এর জন্য)
        if ($request->ajax()) {
            return response()->json([
                'posts' => view('frontend.posts-partial', compact('posts'))->render(),
                'hasMore' => $posts->hasMorePages()
            ]);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
 
    public function sendOtp()
    {
        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }
        $maxAttempts = 10;
        // OTP send info check (session)
        $otpAttempts = session('otp_attempts', []);
        // যদি ইতিমধ্যেই email_verified_at 2 হয়, আর OTP পাঠানো যাবে না
        if ($user->email_verified_at == 2) {
            return back()->with('error', 'You have reached the maximum OTP requests.');
        }
        // পুরানো record remove (optional, 1 hour limit)
        $otpAttempts = array_filter($otpAttempts, function($timestamp) {
            return $timestamp >= now()->subHour()->timestamp;
        });
        if (count($otpAttempts) >= $maxAttempts) {
            // 10 বার OTP send হলে email_verified_at column এ 2 set করা
            $user->email_verified_at = 2;
            $user->save();
            return back()->with('success', 'OTP limit reached. Email marked as verified.');
        }
        // OTP Generate & Save
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->save();
        // Mail send (optional)
        Mail::raw("Your OTP code is: $otp", function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Email Verification - Wihima');
        });
        // Update session attempts
        $otpAttempts[] = now()->timestamp;
        session(['otp_attempts' => $otpAttempts]);
        return back()->with('success', 'OTP sent successfully!');
    }
    
    public function verifyOtp(Request $request)
    {
        $user = Auth::user();
        if($user->otp == $request->otp){
            $user->email_verified_at = 1;
            $user->save();
        }else{
          return back()->with('error', 'your otp is incorrect');
        }
        return back()->with('success', 'otp verify successfully');
    }
    
    public function reSendOtp()
    {
        return $this->sendOtp();
    }
    
    public function index()
    {
        //
    }
    
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
        //
    }
    
    public function getCities($country_id)
    {
        $cities = City::where('country_id', $country_id)->get();
        return response()->json($cities);
    }
    
    public function edit(string $id)
    {
        //
    }
    
    public function update(Request $request, string $id)
    {
        //
    }
    
    public function destroy(string $id)
    {
        //
    }
}