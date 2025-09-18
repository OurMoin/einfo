<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function deliveryIndex()
    {
        return view('frontend.delivery');
    }
    
    public function adminIndex()
    {
        return view('frontend.admin'); // Create this view
    }
}