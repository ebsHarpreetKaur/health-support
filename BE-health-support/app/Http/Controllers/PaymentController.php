<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Eastwest\Json\Facades\Json;
// use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Throwable;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @group Payments
 *
 * APIs for managing payments
 */
class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Payment list
     *
     * This endpoint is used to get user list.
     *
     * 
     * @header Authorization Bearer {token}
     * 
     * @response scenario="Payment list" {
     *   {
     *   "amount": 100.00,
     *   "currency": "USD",
     *   "user_id": "455345532",
     *   "card_number": "4111111111111111",
     *   "card_exp_month": "12",
     *   "card_exp_year": "2025",
     *   "card_cvv": "123",
     *   "billing_address": {
     *       "line1": "123 Billing St",
     *       "line2": "",
     *       "city": "Billing City",
     *       "state": "CA",
     *       "postal_code": "12345",
     *       "country": "US"
     *   },
     *   "description": "Payment for order #12345",
     *   "metadata": {
     *       "order_id": "12345",
     *   }
     *  }
     *
     */
    // Get payment list
    public function Payment_list(Request $request)
    {
        try {
            $validator2 = Validator::make($request->all(),[
                'amount' => 'required|numeric|min:1',
                'currency' => 'required|string',
                'card_number' => 'required|string',
                'card_exp_month' => 'required|integer',
                'card_exp_year' => 'required|integer',
                'card_cvv' => 'required|string',
                'billing_address' => 'required|string',
                'description' => 'required|string',
                'metadata' => 'nullable|array',
                'payment_status' => 'nullable|string',
                'date' => 'required|date',
                'user_id' => 'required|integer|exists:users,_id',
            ]);
            $status = true;

            if($validator2->fails())
            {
                return response()->json($validator2->errors(), 400);
            }
            $user = User::create([
                'mobile'=>$request->mobile,
                'otp_status'=>$request->otp_status,
                'user_location'=>$request->user_location,
                'status'=>$request->status,
                'email'=>$request->email,
                'user_pincode'=>$request->user_pincode,
                'name'=>$request->name,
                'payment_res'=>$request->payment_res,
                'payment_status'=>$request->payment_status,

            ]);
            $token = Auth::guard('api')->login($user);
            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
                'user' => $user,
                'token_type' => 'Bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
                   

    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process request.', 'message' => $e->getMessage()], 500);
        }
    }
    

    public function MakePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_id' => 'required|exists:users,_id',
            'comment' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user_id = Auth::id();
        $existingReview = Review::where('user_id', $user_id)
            ->where('dealer_id', $request->dealer_id)
            ->first();

        if ($existingReview) {
            $existingReview->comment = $request->comment;
            $existingReview->save();
            return response()->json(['message' => 'Review updated successfully', 'review' => $existingReview], 200);
        }

        // Create new review
        $review = new Review();
        $review->user_id = $user_id;
        $review->dealer_id = $request->dealer_id;
        $review->comment = $request->comment;
        $review->save();

        return response()->json(['message' => 'Review stored successfully', 'review' => $review], 201);
    }


    
   /**
     * Update a payment
     *
     * This endpoint is used to update details of a specific payment.
     * 
     * @header Authorization Bearer {token}
     * 
     * @urlParam _id required The ID of the user to update Example: 111
     * @bodyParam mobile integer required digits:10 Example: 2093235874
     * @bodyParam otp_status boolean required Example: false
     * @bodyParam user_location array required 
     * @bodyParam status integer required Example: true
     *
     * @response {
     *    "message": "Payment updated successfully"
     * }
     */
    // update payment by id 
    public function updatePayment(Request $request, $id)
        {
            try {
                $user = User::findOrFail($id);
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'image' => 'nullable|image|max:2048', 
                    'email' => 'nullable|string|email|unique:users,email,' . $user->id,
                    'user_pincode' => 'nullable|integer|max:999999',
                    'status' => 'boolean',
                ], [
                    'name.required' => 'The name field is required.',
                    'name.string' => 'The name must be a string.',
                    'email.email' => 'The email must be a valid email address.',
                    'email.unique' => 'The email has already been taken.',
                    'user_pincode.integer' => 'The pincode must be a integer.',
                    'status.boolean' => 'The status must be a boolean value.',

                ]);
                if ($validator->fails()) {
                    return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 400);
                }
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = $image->getClientOriginalName();
                    $imagePath = $image->storeAs('images', $imageName, 'public/images');
                    $user->image = $imagePath;
                }
                if (empty($user->user_pincode) && !empty($request->user_pincode)) {
                    $user->user_pincode = $request->user_pincode;
                }
                $user->name = $request->name;
                $user->user_pincode = $request->user_pincode ?? $user->user_pincode; 
                $user->status = $request->status ?? $user->status; 

                if (!empty($request->email)) {
                    $user->email = $request->email;
                }
                $user->save();
                return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to update user.', 'error' => $e->getMessage()], 500);
            }
        }


    /**
     * Delete a payment
     *
     * @header Authorization Bearer {token}
     * 
     * This endpoint is used to delete specific payment.
     * 
     * @urlParam id required The ID of the payment to delete. Example: 2
     *
     * @response {
     *    "message": "Payment deleted successfully"
     * }
     */
    // Delete payment by id
    public function deletePaymentById(Request $request, $id)
    {
        try {
            $payment = Payment::find($id);
            if (!$payment) {
                return response()->json(['message' => 'Payment not found.'], 404);
            }

            $payment->delete();

            return response()->json(['message' => 'Payment deleted successfully.'], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to delete payment.', 'error' => $e->getMessage()], 500);
        }
    }





    /**
     * Get a specific payment
     * 
     * 
     * @header Authorization Bearer {token}
     * 
     * 
     * Get the details of a specific payment.
     * 
     * @urlParam id required The ID of the payment. Example: 3
     * 
     * "payment" :  {
     *   "amount": 100.00,
     *   "currency": "USD",
     *   "user_id": "455345532",
     *   "card_number": "4111111111111111",
     *   "card_exp_month": "12",
     *   "card_exp_year": "2025",
     *   "card_cvv": "123",
     *   "billing_address": {
     *       "line1": "123 Billing St",
     *       "line2": "",
     *       "city": "Billing City",
     *       "state": "CA",
     *       "postal_code": "12345",
     *       "country": "US"
     *   },
     *   "description": "Payment for order #12345",
     *   "metadata": {
     *       "order_id": "12345",
     *   }
     *  }
     * @response 404 {
     *   "message": "Payment not found"
     * }
     */
    // get single by id
    public function getPaymentById(Request $request , $id)
    {
        try {

            $user = User::with('properties')->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            return response()->json(['user' => $user], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to retrieve user.', 'error' => $e->getMessage()], 500);
        }
    }



    
    /**
     * Get payments of specific user
     * 
     * 
     * @header Authorization Bearer {token}
     * 
     * 
     * Get the details of a specific payment of user.
     * 
     * @urlParam user_id required The User ID of the User. Example: 3
     * 
     * "payment" :  {
     *   "amount": 100.00,
     *   "currency": "USD",
     *   "user_id": "455345532",
     *   "card_number": "4111111111111111",
     *   "card_exp_month": "12",
     *   "card_exp_year": "2025",
     *   "card_cvv": "123",
     *   "billing_address": {
     *       "line1": "123 Billing St",
     *       "line2": "",
     *       "city": "Billing City",
     *       "state": "CA",
     *       "postal_code": "12345",
     *       "country": "US"
     *   },
     *   "description": "Payment for order #12345",
     *   "metadata": {
     *       "order_id": "12345",
     *   }
     *  }
     * @response 404 {
     *   "message": "Payment record not found for this user"
     * }
     */
    // get specific payment of user
    public function getPaymentsByUserID(Request $request , $user_id)
    {
        try {

            $user = User::with('user_id')->find($user_id);

            if (!$user) {
                return response()->json(['message' => 'Payment record not found for this user.'], 404);
            }

            return response()->json(['user' => $user], 200);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to retrieve payment of this user.', 'error' => $e->getMessage()], 500);
        }
    }


    
}









