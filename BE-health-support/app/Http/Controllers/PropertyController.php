<?php

namespace App\Http\Controllers;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth; 


/**
 * @group Properties
 *
 * APIs for managing properties
 */
class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Property list
     *
     * This endpoint is used to get propeerty list.
     *
     * 
     * @header Authorization Bearer {token}
     * 
     * @response scenario="Property list" 
     * "data": [
     *   "current_page": 1,
     *   "data": [
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location":  [
     *       {
     *           "coords": {
     *               "speed": -1,
     *               "longitude": 76.69112317715411,
     *               "latitude": 30.71134927265382,
     *               "accuracy": 16.965582688710988,
     *               "heading": -1,
     *               "altitude": 318.2151985168457,
     *               "altitudeAccuracy": 7.0764055252075195
     *           },
     *           "timestamp": 1709037095653.2131
     *       }
     *   ],
     *           "bedrooms": "4",
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *            "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }
     *   ],
     *   "first_page_url": "http://127.0.0.1:8000/api/properties?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http://127.0.0.1:8000/api/properties?page=1",
     *   "links": [
     *       {
     *           "url": null,
     *           "label": "&laquo; Previous",
     *           "active": false
     *       },
     *       {
     *           "url": "http://127.0.0.1:8000/api/properties?page=1",
     *           "label": "1",
     *           "active": true
     *       },
     *       {
     *           "url": null,
     *           "label": "Next &raquo;",
     *           "active": false
     *       }
     *   ],
     *   "next_page_url": null,
     *   "path": "http://127.0.0.1:8000/api/properties",
     *   "per_page": 10,
     *   "prev_page_url": null,
     *   "to": 1,
     *   "total": 1
     * }
     *
     */
    public function property_list(Request $request)
    {
        // print_r($request);
        try {
            $sortBy = $request->input('sort_by', 'id'); 
            $sortOrder = $request->input('sort_order', 'asc'); 
            $perPage = $request->input('per_page', 10);
            $userCity = $request->input('user_city');
            $pincode = $request->input('pincode');
            $user_id = $request->input('user_id');
            $type = $request->input('type');
            $propertyName = $request->input('property_name');
    
            // $properties = Property::query()
            //     ->select('properties.*', 'users.name as user_name', 'users.image as user_image', 'users.user_city')
            //     ->leftJoin('users', 'properties.user_id', '=', 'users._id');
    
            if ($userCity) {
                $data = Property::where('user_city', $userCity)->first();
                return response()->json([
                    'message'=>'Properties using user city',
                    'result'=> $data
                ]);
            }
            else if ($pincode) {
                $data = Property::where('pincode', $pincode)->first();
                return response()->json([
                    'message'=>'Properties using pincode',
                    'result'=> $data
                ]);
            }
            else if ($user_id) {
                $data = Property::where('user_id', $user_id)->first();
                return response()->json([
                    'message'=>'Properties using user_id',
                    'result'=> $data,

                ]);
            }
            else if ($type) {
                $data = Property::where('type', $type)->first();
                return response()->json([
                    'message'=>'Properties using type',
                    'result'=> $data
                ]);
            }
            else if ($propertyName) {
                $data = Property::where('property_name', $propertyName)->first();
                return response()->json([
                    'message'=>'Properties using property name',
                    'property name'=> $propertyName,
                    'result'=> $data
                ]);
            }else {
                // Get orderby and pagination by adding into url => ?sort_by=name&sort_order=desc&per_page=20
                $properties = Property::all();
    
                $properties = Property::orderBy($sortBy, $sortOrder)->get();
        
                $properties = Property::orderBy($sortBy, $sortOrder)->paginate($perPage);
        
                return response()->json([
                    'data' => $properties,
                ], 200);

        }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve properties.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    
     
    /**
     * Add a property
     *
     * This endpoint is used to add a property.
     * 
     * payload : 
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location":  [
     *       {
     *           "coords": {
     *               "speed": -1,
     *               "longitude": 76.69112317715411,
     *               "latitude": 30.71134927265382,
     *               "accuracy": 16.965582688710988,
     *               "heading": -1,
     *               "altitude": 318.2151985168457,
     *               "altitudeAccuracy": 7.0764055252075195
     *           },
     *           "timestamp": 1709037095653.2131
     *       }
     *   ]
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *           "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }
     * 
     * @header Authorization Bearer {token}
     * 
     * 
     * @bodyParam user_id integer required Example: 4547475
     * @bodyParam property_name string required Example: prop1
     * @bodyParam price integer required Example: 57787888
     * @bodyParam location array required Example: Mohali
     * @bodyParam bedrooms integer required Example: 3
     * @bodyParam bathrooms integer required Example: 2
     * @bodyParam area_sqft integer required Example: 22323
     * @bodyParam deal string required Example: sale
     * @bodyParam type string required Example: villa
     * @bodyParam parking integer required Example: 4
     * @bodyParam description string required Example: This property is for sale
     * @bodyParam assigned_buyer string required Example: Joh doe
     * @bodyParam isAvailable boolean required Example: true
     * @bodyParam dealer string required  Example: Iseak Huii
     * @bodyParam dealer_contact integer digits:10 required  Example: 5666566565
     * @bodyParam district string required  Example: Mohali
     * @bodyParam property_details array required
     * @bodyParam images file required
     * @bodyParam photo file required 
     *
     * @response 
     *    
     * "result": 
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location": "\"{\\\"latitude\\\": 40.7128, \\\"longitude\\\": -74.0060}\"",
     *           "bedrooms": "4",
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *           "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }
     */
    public function storeProperty(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,_id',
            'property_name' => 'required|string|min:4|max:40',
            'price' => 'required|numeric|min:0', 
            'location' => 'required', 
            'bedrooms' => 'required|integer|min:0', 
            'bathrooms' => 'required|integer|min:0', 
            'area_sqft' => 'required|numeric|min:0', 
            'deal' => 'required|string|in:sale,rent', 
            'type' => 'required|string|in:house,apartment,villa', 
            'parking' => 'required|integer|min:0', 
            'description' => 'required|string|min:5',
            'assigned_buyer' => 'nullable|string',
            'isAvailable' => 'boolean',
            'dealer' => 'nullable|string',
            'dealer_contact' => 'required|numeric|digits_between:10,10', 
            'district' => 'nullable|string',
            'property_details' => 'required|array',
            'photo'=> 'required',
            'property_details.city_view' => 'nullable|string', 
            'property_details.family_villa' => 'nullable|string',
            'property_details.air_conditioned' => 'nullable|string',
            'property_details.phone' => 'nullable|integer||digits:10',
            'property_details.internet' => 'nullable|string',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 400);
            }else{

            if ($request->hasFile('images')) {
                $multiple_photos = [];
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->hashName();
                    $path = $image->move(public_path('images/property_images'), $filename);
    
                    // Add the filename to the array
                    $multiple_photos[] = 'images/property_images/' . $filename;
                }

                if ($request->hasFile('photo')) {
                    
                    $Uploadimage = $request->file('photo');
                    $single_photo = time() . '_' . $Uploadimage->hashName();
                    $Uploadimage->move(public_path('images/property_default_image'), $single_photo);
                
                    $photo = 'images/property_default_image/' . $single_photo;


                    $locationString = $request->input('location');
                    $locationArray = json_decode($locationString, true);

                        // single image upload
                    

                        $property = new Property();
                        $property->user_id = $request->user_id;
                        $property->property_name = $request->property_name;
                        $property->price = $request->price;
                        $property->location = $locationArray;
                        $property->bedrooms = $request->bedrooms;
                        $property->bathrooms = $request->bathrooms;
                        $property->area_sqft = $request->area_sqft;
                        $property->deal = $request->deal;
                        $property->type = $request->type;
                        $property->parking = $request->parking;
                        $property->description = $request->description;
                        $property->assigned_buyer = $request->assigned_buyer;
                        $property->isAvailable = $request->isAvailable;
                        $property->dealer = $request->dealer;
                        $property->dealer_contact = $request->dealer_contact;
                        $property->district = $request->district;
                        $property->property_details = $request->property_details;
                        $property->images = $multiple_photos;
                        $property->photo = $photo;
                        $property->save();

                        return response()->json([
                            'message'=>'Property added successfully.',
                            'result'=> $property,
                            'property_default_path' => $photo,
                            'multiple_photos_paths' => $multiple_photos
                        ]);
                 
                }else {
                    return response()->json(['message' => 'No file uploaded'], 400);
                }
            } else {
                return response()->json(['message' => 'No images provided'], 400);
            }

        }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store property.', 'error' => $e->getMessage()], 500);
        }
    }
    


    /**
     * Delete a property
     *
     * @header Authorization Bearer {token}
     * 
     * This endpoint is used to delete specific property.
     * 
     * @urlParam id required The ID of the property to delete. Example: 2
     *
     * @response {
     *    "message": "Property deleted successfully"
     * }
     */
    public function deletePropertyById($id)
    {
        try {
            $property = Property::find($id);
    
            if (!$property) {
                return response()->json(['message' => 'Property not found.'], 404);
            }
    
            $property->delete();
    
            return response()->json(['message' => 'Property deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete property.', 'error' => $e->getMessage()], 500);
        }
    }
    



    
    /**
     * Update a property
     *
     * This endpoint is used to update details of a specific property.
     * 
     * payload : 
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location":  [
     *       {
     *           "coords": {
     *               "speed": -1,
     *               "longitude": 76.69112317715411,
     *               "latitude": 30.71134927265382,
     *               "accuracy": 16.965582688710988,
     *               "heading": -1,
     *               "altitude": 318.2151985168457,
     *               "altitudeAccuracy": 7.0764055252075195
     *           },
     *           "timestamp": 1709037095653.2131
     *       }
     *   ]
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *           "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }
     * 
     * @header Authorization Bearer {token}
     * 
     * @urlParam id int required The ID of property to update Example: 452
     * 
     * @bodyParam user_id integer required Example: 4547475
     * @bodyParam property_name string required Example: prop1
     * @bodyParam price integer required Example: 57787888
     * @bodyParam location array required Example: Mohali
     * @bodyParam bedrooms integer required Example: 3
     * @bodyParam bathrooms integer required Example: 2
     * @bodyParam area_sqft integer required Example: 22323
     * @bodyParam deal string required Example: sale
     * @bodyParam type string required Example: villa
     * @bodyParam parking integer required Example: 4
     * @bodyParam description string required Example: This property is for sale.
     * @bodyParam assigned_buyer string required Example: Joh doe
     * @bodyParam isAvailable boolean required Example: true
     * @bodyParam dealer string required  Example: Iseak Huii
     * @bodyParam dealer_contact integer digits:10 required  Example: 5666566565
     * @bodyParam district string required  Example: Mohali
     * @bodyParam property_details array required  Example: {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           }
     * @bodyParam images file required  Example: [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ]
     * @bodyParam photo file required
     * 
     * 
     * "result": 
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location": "\"{\\\"latitude\\\": 40.7128, \\\"longitude\\\": -74.0060}\"",
     *           "bedrooms": "4",
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *           "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }
     * 
     */
    // Update property details
    // public function updateProperty(Request $request, $id)
    // {
    //     try {
    //         // Validate request data
    //         $validator = Validator::make($request->all(), [
    //             'user_id' => 'required|string|exists:users,_id',
    //             'property_name' => 'required|string|min:4|max:40',
    //             'price' => 'required|numeric|min:0',
    //             'location' => 'required',
    //             'bedrooms' => 'required|integer|min:0',
    //             'bathrooms' => 'required|integer|min:0',
    //             'area_sqft' => 'required|numeric|min:0',
    //             'deal' => 'required|string|in:sale,rent',
    //             'type' => 'required|string|in:house,apartment,villa',
    //             'parking' => 'required|integer|min:0',
    //             'description' => 'required|string|min:5',
    //             'assigned_buyer' => 'nullable|string',
    //             'isAvailable' => 'boolean',
    //             'dealer' => 'nullable|string',
    //             'dealer_contact' => 'required|numeric|digits_between:10,10',
    //             'district' => 'nullable|string',
    //             'property_details' => 'required|array',
    //             'photo' => 'required',
    //             'property_details.city_view' => 'nullable|string',
    //             'property_details.family_villa' => 'nullable|string',
    //             'property_details.air_conditioned' => 'nullable|string',
    //             'property_details.phone' => 'nullable|integer|digits:10',
    //             'property_details.internet' => 'nullable|string',
    //             'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 400);
    //         }
    
    //         // Find the property by ID
    //         $property = Property::find($id);
    
    //         if (!$property) {
    //             return response()->json(['message' => 'Property not found'], 404);
    //         }
    
    //         // Update property fields
    //         $property->user_id = $request->user_id;
    //         $property->property_name = $request->property_name;
    //         $property->price = $request->price;
    //         $property->location = json_decode($request->input('location'), true);
    //         $property->bedrooms = $request->bedrooms;
    //         $property->bathrooms = $request->bathrooms;
    //         $property->area_sqft = $request->area_sqft;
    //         $property->deal = $request->deal;
    //         $property->type = $request->type;
    //         $property->parking = $request->parking;
    //         $property->description = $request->description;
    //         $property->assigned_buyer = $request->assigned_buyer;
    //         $property->isAvailable = $request->isAvailable;
    //         $property->dealer = $request->dealer;
    //         $property->dealer_contact = $request->dealer_contact;
    //         $property->district = $request->district;
    //         $property->property_details = $request->property_details;
    
    //         // Handle image uploads
    //         if ($request->hasFile('images')) {
    //             $multiple_photos = [];
    //             foreach ($request->file('images') as $image) {
    //                 $filename = time() . '_' . $image->hashName();
    //                 $image->move(public_path('images/property_images'), $filename);
    
    //                 // Add the filename to the array
    //                 $multiple_photos[] = 'images/property_images/' . $filename;
    //             }
    //             $property->images = $multiple_photos;
    //         }
    
    //         if ($request->hasFile('photo')) {
    //             $Uploadimage = $request->file('photo');
    //             $single_photo = time() . '_' . $Uploadimage->hashName();
    //             $Uploadimage->move(public_path('images/property_default_image'), $single_photo);
    
    //             $photo = 'images/property_default_image/' . $single_photo;
    //             $property->photo = $photo;
    //         }
    
    //         // Save the updated property
    //         $property->save();
    
    //         return response()->json([
    //             'message' => 'Property updated successfully.',
    //             'result' => $property,
    //         ]);
    
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Failed to update property.', 'error' => $e->getMessage()], 500);
    //     }
    // }
    
    public function updateProperty(Request $request, $id)
        {
            try {
                // Validate request data
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|string|exists:users,_id',
                    'property_name' => 'required|string|min:4|max:40',
                    'price' => 'required|numeric|min:0',
                    'location' => 'required',
                    'bedrooms' => 'required|integer|min:0',
                    'bathrooms' => 'required|integer|min:0',
                    'area_sqft' => 'required|numeric|min:0',
                    'deal' => 'required|string|in:sale,rent',
                    'type' => 'required|string|in:house,apartment,villa',
                    'parking' => 'required|integer|min:0',
                    'description' => 'required|string|min:5',
                    'assigned_buyer' => 'nullable|string',
                    'isAvailable' => 'boolean',
                    'dealer' => 'nullable|string',
                    'dealer_contact' => 'required|numeric|digits_between:10,10',
                    'district' => 'nullable|string',
                    'property_details' => 'nullable|array',
                    'photo' => 'nullable',
                    'property_details.city_view' => 'nullable',
                    'property_details.family_villa' => 'nullable',
                    'property_details.air_conditioned' => 'nullable',
                    'property_details.phone' => 'nullable|integer|digits:10',
                    'property_details.internet' => 'nullable|string',
                    'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 400);
                }

                // Find the property by ID
                $property = Property::find($id);

                if (!$property) {
                    return response()->json(['message' => 'Property not found'], 404);
                }

                // Update property fields
                $property->user_id = $request->user_id;
                $property->property_name = $request->property_name;
                $property->price = $request->price;
                $property->location = json_decode($request->input('location'), true);
                $property->bedrooms = $request->bedrooms;
                $property->bathrooms = $request->bathrooms;
                $property->area_sqft = $request->area_sqft;
                $property->deal = $request->deal;
                $property->type = $request->type;
                $property->parking = $request->parking;
                $property->description = $request->description;
                $property->assigned_buyer = $request->assigned_buyer;
                $property->isAvailable = $request->isAvailable;
                $property->dealer = $request->dealer;
                $property->dealer_contact = $request->dealer_contact;
                $property->district = $request->district;
                $property->property_details = $request->property_details;

                // Handle image uploads
                if ($request->hasFile('images')) {
                    $multiple_photos = [];
                    foreach ($request->file('images') as $image) {
                        $filename = time() . '_' . $image->hashName();
                        $image->move(public_path('images/property_images'), $filename);

                        // Add the filename to the array
                        $multiple_photos[] = 'images/property_images/' . $filename;
                    }
                    $property->images = $multiple_photos;
                }

                if ($request->hasFile('photo')) {
                    $Uploadimage = $request->file('photo');
                    $single_photo = time() . '_' . $Uploadimage->hashName();
                    $Uploadimage->move(public_path('images/property_default_image'), $single_photo);

                    $photo = 'images/property_default_image/' . $single_photo;
                    $property->photo = $photo;
                }

                // Save the updated property
                $property->save();

                return response()->json([
                    'message' => 'Property updated successfully.',
                    'result' => $property,
                ]);

            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to update property.', 'error' => $e->getMessage()], 500);
            }
        }

    


    
    /**
     * Get a specific property
     * 
     * 
     * @header Authorization Bearer {token}
     * 
     * 
     * Get the details of a specific property.
     * 
     * @urlParam id required The ID of the property. Example: 345
     * 
     * "user" :
     *       {
     *           "_id": "65eec2cd8fb87b059e0c70d2",
     *           "user_id": "65eec0aa4b28c694b60ec10c",
     *           "property_name": "hs villa",
     *           "price": "9867679",
     *           "location": "\"{\\\"latitude\\\": 40.7128, \\\"longitude\\\": -74.0060}\"",
     *           "bedrooms": "4",
     *           "bathrooms": "2",
     *           "area_sqft": "7843784378",
     *           "deal": "rent",
     *           "type": "house",
     *           "parking": "2",
     *           "description": "check descrption",
     *           "assigned_buyer": null,
     *           "isAvailable": null,
     *           "dealer": null,
     *           "dealer_contact": "8899488944",
     *           "district": null,
     *           "property_details": {
     *               "city_view": null,
     *               "family_villa": "Family Villa",
     *               "air_conditioned": "Air Conditioned",
     *               "phone": "3455467676",
     *               "internet": "yes"
     *           },
     *           "images": [
     *               "property_images/1710146253_3mLKPnUdwwBHXOQFpaT64uY4QtnwRTHBY382VX6C.png",
     *               "property_images/1710146253_79LV6xbjmuu8feXKfbXwdRAlEwLjs23pGTTaYYaa.png"
     *           ],
     *           "photo": "u4hMHWkJXHNM6j1nJIplNxXnG4wcYk2w7kpKCPt9.png",
     *           "updated_at": "2024-03-11T08:37:33.932000Z",
     *           "created_at": "2024-03-11T08:37:33.932000Z"
     *       }     *   "mobile": 3294839384,
     *   "name": null,
     *   "email": null,
     *   "image": null,
     *   "user_pincode": null,
     *       "longitude": -74.006
     *   "user_location": {
     *       "latitude": 40.7128,
     *   },
     *   "rera_number": 6767,
     *   "user_city": "una",
     *   "otp_status": true,
     *   "status": true,
     *   "updated_at": "2024-03-11T08:28:26.326000Z",
     *   "created_at": "2024-03-11T08:28:26.326000Z"
     *  },
     * @response 404 {
     *   "message": "Property not found"
     * }
     */
    public function getPropertiesByUserId($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
            $property = Property::find($id);
            if (!$property) {
                return response()->json(['message' => 'Property not found.'], 404);
            }
            if ($property->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
    
            return response()->json(['message' => 'Property found.', 'property' => $property], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch property.', 'error' => $e->getMessage()], 500);
        }
    }
    

    
    
}
