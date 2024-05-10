<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class DealersController extends Controller
{
    /**
     * Display a listing of the dealers.
     *
     * @return \Illuminate\View\View
     */
    public function dealers(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');
    
        // Search functionality
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('email', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('mobile', 'like', '%' . $request->input('search') . '%');
        }
    
        // Paginate the results
        $users = $query->paginate(10);
    
        // Pass user data and search query to the view
        return view('dealer.index', [
            'users' => $users,
            'search' => $request->input('search')
        ]);
    }
    

    /**
     * Show the form for creating a new dealer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('dealer.create');
    }

    /**
     * Store a newly created dealer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

     public function save(Request $request)
     {
         try {
             $validator = Validator::make($request->all(), [
                 'name' => 'required|string|max:255',
                 'email' => 'nullable|email|unique:users,email',
                 'user_pincode' => 'nullable|integer|max:999999',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5242880',
                 'mobile' => 'required|integer|unique:users,mobile|digits:10',
                 'password' => 'required|string|min:6',
                //  'otp_status' => 'nullable|boolean',
                //  'status' => 'nullable|boolean',
                 'role' => 'required|string|in:admin,user',
             ]);
     
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            $validatedData = $validator->validated();
    
             // Sanitize and validate otp_status and status
                    // Set default values
            $validatedData['otp_status'] = true;
            $validatedData['status'] = true;
            $validatedData['mobile'] = intval(filter_var($validatedData['mobile'], FILTER_SANITIZE_NUMBER_INT));
            $validatedData['email'] = filter_var($validatedData['email'], FILTER_SANITIZE_EMAIL);
            $validatedData['name'] = filter_var($validatedData['name'], FILTER_SANITIZE_STRING);
            $validatedData['user_city'] = filter_var($request->user_city, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['user_pincode'] = intval(filter_var($request->user_pincode, FILTER_SANITIZE_NUMBER_INT)) ?? "N/A";
            $validatedData['rera_number'] = filter_var($request->rera_number, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['password'] = Hash::make($request->password);
            $validatedData['image'] = filter_var($request->image, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['payment_res'] = filter_var($request->payment_res, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['payment_status'] = filter_var($request->payment_status, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['status'] = filter_var($request->status, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['average_user_rating'] = filter_var($request->average_user_rating, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['ratings'] = filter_var($request->ratings, FILTER_SANITIZE_STRING) ?? "N/A";
            $validatedData['user_location'] = filter_var($request->user_location, FILTER_SANITIZE_STRING) ?? "N/A";

            // Create a new user
            User::create($validatedData);
    
            return redirect()->route('dealers')->with('success', 'Dealer created successfully');
         } catch (\Exception $e) {
             return redirect()->back()->withErrors(['error' => 'Failed to create dealer: ' . $e->getMessage()])->withInput();
         }
     }
     
     

     
     
     
    

    /**
     * Show the form for editing the specified dealer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $dealer = User::findOrFail($id);
        
        return view('dealer.edit', ['dealer' => $dealer]);
    }
    

    /**
     * Update the specified dealer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

     public function updateDealer(Request $request, $id)
     {
         try {
             $rules = [
                 'name' => 'nullable|string|max:255',
                 'mobile' => 'required|integer|digits:10' ,
                //  'email' => 'nullable|email|unique:users,email,' . $id,
                 'user_city' => 'nullable|string|max:255',
                 'user_pincode' => 'nullable|integer|max:999999',
                 'rera_number' => 'nullable|string|max:255',
                 'status' => 'nullable|boolean',
                 'role' => 'nullable|string|in:admin,user',
                 'password' => 'nullable|string|min:6',
                 'otp_status' => 'nullable|boolean',
                 'payment_res' => 'nullable|string|max:255',
                 'payment_status' => 'nullable|string|in:pending,completed,failed',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the max file size as needed
             ];
     
             $validator = Validator::make($request->all(), $rules);
     
             if ($validator->fails()) {
                 return redirect()->back()
                     ->withErrors($validator)
                     ->withInput();
             }
     
             $dealer = User::find($id);
     
             if (!$dealer) {
                 return response()->json(['message' => 'Dealer not found'], 404);
             }
     
             // Update fields only if new data is provided or existing data is changed
             $dealer->mobile = $request->filled('mobile') ? intval($request->mobile) : $dealer->mobile;
             $dealer->name = $request->filled('name') ? filter_var($request->name, FILTER_SANITIZE_STRING) : $dealer->name;
            //  $dealer->email = $request->filled('email') && $request->email != $dealer->email ? filter_var($request->email, FILTER_SANITIZE_EMAIL) : $dealer->email;
             $dealer->user_city = $request->filled('user_city') ? filter_var($request->user_city, FILTER_SANITIZE_STRING) : $dealer->user_city;
             $dealer->role = $request->filled('role') ? filter_var($request->role, FILTER_SANITIZE_STRING) : $dealer->role;
             $dealer->status = $request->filled('status') ? filter_var($request->status, FILTER_VALIDATE_BOOLEAN) : $dealer->status;
             $dealer->user_pincode = $request->filled('user_pincode') ? intval($request->user_pincode) : $dealer->user_pincode;
             $dealer->rera_number = $request->filled('rera_number') ? filter_var($request->rera_number, FILTER_SANITIZE_STRING) : $dealer->rera_number;
             $dealer->password = $request->filled('password') ? Hash::make($request->password) : $dealer->password;
             $dealer->otp_status = $request->filled('otp_status') ? filter_var($request->otp_status, FILTER_VALIDATE_BOOLEAN) : $dealer->otp_status;
             $dealer->payment_res = $request->filled('payment_res') ? filter_var($request->payment_res, FILTER_SANITIZE_STRING) : $dealer->payment_res;
             $dealer->payment_status = $request->filled('payment_status') ? filter_var($request->payment_status, FILTER_SANITIZE_STRING) : $dealer->payment_status;
     
             // Handle image upload
             if ($request->hasFile('image')) {
                 $Uploadimage = $request->file('image');
                 $single_photo = time() . '_' . $Uploadimage->hashName();
                 if (!empty($dealer->image)) {
                     $oldImagePath = public_path('images/user_images/' . $dealer->image);
                     if (file_exists($oldImagePath)) {
                         unlink($oldImagePath);
                     }
                 }
                 $Uploadimage->move(public_path('images/user_images'), $single_photo);
                 $dealer->image = 'images/user_images/' . $single_photo;
             }
     
             $dealer->save();
     
             return redirect()->route('dealers')->with('success', 'Dealer updated successfully.');
     
         } catch (\Exception $e) {
             return redirect()->back()
                 ->withErrors(['error' => 'Failed to update dealer: ' . $e->getMessage()])
                 ->withInput();
         }
     }
     
     

     
     

    
    
    
    

    

    

    /**
     * Remove the specified dealer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
{
    try {
        $dealer = User::findOrFail($id);

        // if ($dealer->role === 'admin') {
        //     return redirect()->route('dealers')
        //         ->withErrors(['error' => 'This user is an admin. Are you sure you want to delete?'])
        //         ->with('adminDeleteConfirmation', true);
        // }

        $dealer->delete();

        return redirect()->route('dealers')->with('success', 'Dealer deleted successfully');
    } catch (\Exception $e) {
        return redirect()->route('dealers')->withErrors(['error' => 'Failed to delete dealer: ' . $e->getMessage()]);
    }
}

    
}
