<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User; // Add this line

class DeliveryController extends Controller
{
    public function deliveryIndex()
    {
        return view('frontend.delivery');
    }
   
    public function adminIndex()
    {
        $users = User::with(['country', 'city', 'category'])->paginate(10);
        return view('frontend.admin', compact('users'));
    }
}