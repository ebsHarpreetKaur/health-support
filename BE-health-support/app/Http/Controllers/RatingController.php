<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
  
    public function store(Request $request)
    {
        // Validation rules
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,_id',
            'dealer_id' => 'required|exists:users,_id',
            'rating' => 'required|integer|between:1,5',
        ]);
    
        // Check if validation fails
        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 422);
        }
    
        // Check if user exists
        if (!User::where('_id', $request->input('user_id'))->exists()) {
            return response()->json(['error' => 'Invalid user ID'], 400);
        }
    
        // Check if dealer exists
        if (!User::where('_id', $request->input('dealer_id'))->exists()) {
            return response()->json(['error' => 'Invalid dealer ID'], 400);
        }
    
        // Check if the rating already exists
        $existingRating = Rating::where('user_id', $request->input('user_id'))
                                ->where('dealer_id', $request->input('dealer_id'))
                                ->first();
    
        if ($existingRating) {
            // If the rating exists, update it
            $existingRating->rating = $request->input('rating');
            $existingRating->save();
        } else {
            // If the rating does not exist, create a new one
            $rating = new Rating();
            $rating->user_id = $request->input('user_id');
            $rating->dealer_id = $request->input('dealer_id');
            $rating->rating = $request->input('rating');
            $rating->save();
        }
    
        // Return success response
        return response()->json(['message' => 'Rating saved successfully'], 200);
    }
    
   


 
    
    
    // public function getUserRatings(Request $request)
    // {
    //     try {
    //         $userId = $request->input('user_id');
    
    //         $ratings = Rating::with('user')
    //                          ->where('dealer_id', $userId)
    //                          ->get(['user_id', 'rating', 'created_at', 'updated_at']);
    
    //         $ratingsArray = $ratings->map(function ($rating) {
    //             return [
    //                 'rating' => $rating->rating,
    //                 'user_id' => $rating->user_id,
    //                 'created_at' => Carbon::parse($rating->created_at)->format('Y-m-d H:i:s'),
    //                 'updated_at' => Carbon::parse($rating->updated_at)->format('Y-m-d H:i:s')
    //             ];
    //         });
    
    //         $totalRatingsCount = $ratingsArray->count();
    //         $totalRatingsSum = $ratingsArray->sum('rating');
    //         $uniqueUserCount = $ratingsArray->unique('user_id')->count();
    
    //         $averageRating = ($totalRatingsCount > 0) ? ($totalRatingsSum / $totalRatingsCount) : 0;
    
    //         // Prepare the response data including creation and update time
    //         $responseData = [
    //             'total_users_rated' => $uniqueUserCount,
    //             'total_ratings' => $totalRatingsCount,
    //             'average_rating' => $averageRating,
    //             'ratings' => $ratingsArray->toArray() // Include ratings with formatted creation and update time
    //         ];
    
    //         return response()->json($responseData, 200);
    //     } catch (QueryException $e) {
    //         return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
    //     }
    // }
    

   
    
    public function getUserRatings(Request $request)
    {
        try {
            $userId = $request->input('user_id');
    
            $ratings = Rating::where('dealer_id', $userId)->get();
    
            $totalRatingsCount = $ratings->count();
            $totalRatingsSum = $ratings->sum('rating');
            $uniqueUserCount = $ratings->unique('user_id')->count();
    
            $averageRating = ($totalRatingsCount > 0) ? ($totalRatingsSum / $totalRatingsCount) : 0;
            $totalUsersCount = User::count();
            $totalUsersRatingsSum = Rating::whereIn('user_id', User::pluck('_id'))->sum('rating');

        print($totalUsersRatingsSum);
            $responseData = [
                'total_users_rated' => $uniqueUserCount,
                // 'total_ratings' => $totalRatingsCount,
                'average_rating' => $averageRating,
                'ratings' => $ratings->map(function ($rating) {
                    return [
                        'rating' => $rating->rating,
                        'created_at' => Carbon::parse($rating->created_at)->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::parse($rating->updated_at)->format('Y-m-d H:i:s')
                    ];
                })
            ];
    
         
            $responseData['total_users'] = $totalUsersCount;
            // $responseData['total_users_ratings'] = $totalUsersRatingsSum;
            $responseData['message'] = 'Data retrieved successfully';

            return response()->json($responseData, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
    


    
   
  
    



    

    
    
    
    
    
    
    
}
