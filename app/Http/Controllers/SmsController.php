<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmsService;

class SmsController extends Controller
{
    public function index()
    {
        return view('sms.form');
    }

    public function send(Request $request, SmsService $smsService)
    {
        $request->validate([
            'phone'   => 'required|string',
            'message' => 'required|string|max:255',
        ]);

        $response = $smsService->sendSms($request->phone, $request->message);

        return back()->with('status', 'SMS Sent! Response: ' . json_encode($response));
    }

     public function registerToken(Request $request)
    {
        $user = User::where('username', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Token saved successfully'
        ]);
    }
}
