<?php

namespace App\Http\Controllers\web;
// use Closure;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
 

    public function register()
    {
        return view('auth/register');
    }

    public function registerSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10|unique:users',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string|in:user,admin',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors(['error' => $validator->errors()]);
        }
    
        $user = User::create([
            'mobile' => intval($request->mobile), 
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'role' => $request->role,
            'otp_status' => true,
            'status' => true,
        ]);
    
        return redirect()->route('login')->with([
            'message' => 'User registered successfully',
        ]);
    }
    
    
    

  
    public function login()
    {
        return view('auth/login');
    }
  


    public function loginAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Please enter your password.',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors(['error' => $validator->errors()]);
            }
    
            $email = $request->input('email');
            $password = $request->input('password');
    
            $user = User::where('email', $email)->first();
    
            if (!$user || !Hash::check($password, $user->password)) {
                throw new \Exception('Invalid Email or password');
            }
    
            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                $user = Auth::user();
                Log::info('Authenticated user:', ['user' => $user]); 
    
                if ($user->role === 'admin') {
                    Session::put('user', $user);
    
                    return redirect()->route('dashboard')->with([
                        'message' => 'Admin logged in successfully',
                    ]);
                } else {
                    Auth::logout();
                    throw new \Exception('You do not have permission to access this page.');
                }
            } else {
                throw new \Exception('Authentication failed');
            }
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Custom validation error messages
            return redirect()->back()->withInput()->withErrors(['error' => $e->validator->errors()->first()]);
        } catch (\Exception $e) {
            // General error message
            return redirect()->back()->withInput()->withErrors(['error' => 'Login failed. Please check your credentials and try again.']);
        }
    }
    
    

  
    public function changePasswordView()
{
    return view('change-password');
}
  


public function changePassword(Request $request)
{
    try {
        $mobile = Session::get('user');
        $user = User::where('mobile', $mobile['mobile'])->first();
        if (!$user) {
            return redirect()->back()->withInput()->withErrors(['error' => 'User not found']);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:6',
            'new_password' => 'required|min:6|different:old_password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors(['error' => $validator->errors()]);
        }

        // Check if the old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'The old password is incorrect.']);
        }

        // Update the password
        $update = $user->update([
            'password' => Hash::make($request->new_password),
            'otp_status' => true,
            'status' => true,
        ]);

        if (!$update) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update password. Please try again.']);
        }

        // Logout the user
        $userId = $user->_id; // Assuming '_id' is the user ID field
        $userObject = User::find($userId); // Get the user object again if needed

        // Remove user from the session
        $request->session()->forget('user');

        // Flash a message to the session
        $request->session()->flash('message', 'Password changed successfully. Please login with your new password.');

        // Redirect to login page
        return redirect()->route('login');

    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error('Error changing password: ' . $e->getMessage());

        return redirect()->back()->withInput()->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
    }
}











    



    public function logout(Request $request)
    {
        // Auth::logout(); // Logout the user using Laravel's Auth facade

        // Remove user from the session
        $request->session()->forget('user');

        return redirect()->route('login')->with([
            'message' => 'Logged out successfully'
        ]);
    }

    


   


//     public function index()
// {
//     $user = session('user');
//     $token = session('token');

//     if (!$user || !$token) {
//         return redirect('login')->withErrors(['error' => 'Unauthorized']);
//     }


//     return view('dashboard', compact('user', 'token'));
// }

}
