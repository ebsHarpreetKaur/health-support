<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            ]);
    
            $uploadedImages = [];
    
            // Check if images are provided
            if ($request->hasFile('images') && count($request->file('images')) > 0) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('/assets/images', $imageName, 'public');
                    $imageModel = new Image();
                    $imageModel->path = $imagePath;
                    $imageModel->save();
                    $uploadedImages[] = $imageModel;
                }
    
                $message = empty($request->input('existing_images')) ? 'Images uploaded successfully' : 'Images updated successfully';
                return response()->json(['message' => $message, 'images' => $uploadedImages], 201);
            } else {
                // If no images were provided for upload, return a 400 response with an appropriate message
                return response()->json(['message' => 'No images were provided for upload'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to upload images.', 'error' => $e->getMessage()], 500);
        }
    }
    
}
