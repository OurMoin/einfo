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
}
