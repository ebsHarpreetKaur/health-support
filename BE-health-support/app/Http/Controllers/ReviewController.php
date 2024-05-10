<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class ReviewController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function storeReview(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'dealer_reviews' => 'required|array', 
                'dealer_reviews.*.dealer_id' => 'required|exists:users,_id',
                'dealer_reviews.*.comment' => 'required|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
            }
    
            $authenticatedUserId = Auth::id();
            if (!$authenticatedUserId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $createdReviews = [];
            foreach ($request->dealer_reviews as $dealerReviewData) {
                $review = new Review();
                $review->user_id = $authenticatedUserId;
                $review->dealer_id = $dealerReviewData['dealer_id'];
                $review->comment = $dealerReviewData['comment'];
                $review->save();
                $createdReviews[] = $review;
            }
            return response()->json(['message' => 'Reviews stored successfully.', 'reviews' => $createdReviews], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }
    
    
    
    
    

    public function updateReview(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }
    
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
            }
    
            $authenticatedUserId = Auth::id();
    
            if (!$authenticatedUserId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $review = Review::find($id);
            if (!$review || $review->user_id != $authenticatedUserId) {
                return response()->json(['error' => 'Unauthorized. You do not have permission to update this review.'], 403);
            }
            $review->comment = $request->comment;
            $review->save();
    
            return response()->json(['message' => 'Review updated successfully.', 'review' => $review], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }
    
     


public function deleteReview($id)
{
    try {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
        }
        $authenticatedUserId = Auth::id();
        if (!$authenticatedUserId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $review = Review::find($id);

        if (!$review || $review->user_id != $authenticatedUserId) {
            return response()->json(['error' => 'Unauthorized. You do not have permission to delete this review.'], 403);
        }
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while processing the request.'], 500);
    }
}

    

    
    
}