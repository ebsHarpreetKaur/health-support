<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Property; 
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
   
    public function index()
    {
        $user = session('user');
        $message = session('message');
        
        $users = User::all();
        
        $properties = Property::all();
        $activeUsersCount = User::where('status', true)->count();
        $inactiveUsersCount = User::where('status', false)->count();
        $propertyDealers = User::where('role', 'property_dealer')->get();
        
        return view('dashboard', [
            'user' => $user,
            'message' => $message,
            'users' => $users,
            'properties' => $properties,
            'propertyDealers' => $propertyDealers,
            'activeUsersCount' => $activeUsersCount,
            'inactiveUsersCount' => $inactiveUsersCount,
        ]);
    }
    
    




    public function profile()
    {
        return view('profile');
    }



    public function downloadAPK()
    {
        $apkPath = public_path('assets/apk/unify-prod.apk');

        if (file_exists($apkPath)) {
            return response()->download($apkPath, 'unify-prod.apk');
        } else {
            return response()->json(['error' => 'APK file not found'], 404);
        }
    }
  
    
    
}
