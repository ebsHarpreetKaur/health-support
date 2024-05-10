<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class PropertyController extends Controller
{
  
    public function index()
    {
        $properties = Property::orderBy('_id', 'desc')->get();
        $total = Property::count();
        return view('property.home', compact(['properties', 'total']));
    }

    public function create()
    {
        return view('property.create');
    }

        public function save(Request $request)
        {

            // dd($request);
            $validator = Validator::make($request->all(), [
            'property_name' => 'required|string|min:4|max:40',
            'price' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area_sqft' => 'required|numeric|min:0',
            'deal' => 'required|in:sale,rent',
            'type' => 'required|in:house,apartment,villa',
            'parking' => 'required|integer|min:0',
            'description' => 'required|string|min:4',
            'dealer_contact' => 'required|numeric|digits:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'assigned_buyer' => 'nullable|string',
            'isAvailable' => 'boolean',
            'dealer' => 'nullable|string',
            'district' => 'required|string',
            'property_details' => 'required|array',
            'property_details.city_view' => 'required|string',
            'property_details.family_villa' => 'required|string',
            'property_details.air_conditioned' => 'required|string',
            'property_details.phone' => 'required|numeric|digits:10',
            'property_details.internet' => 'required|string',
        ], [
            // Custom messages for validation rules
            'required' => 'Please fill in the :attribute field.',
            'string' => 'Please enter a valid :attribute.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be longer than :max characters.',
            'numeric' => 'Please enter a number for :attribute.',
            'integer' => 'Please enter a whole number for :attribute.',
            'in' => 'Please select a valid :attribute.',
            'digits' => 'The :attribute must be :digits digits.',
            'image' => 'Please upload a valid image for :attribute.',
            'mimes' => 'Please upload a file of type: :values for :attribute.',
            'max' => 'The :attribute file size must not exceed :max kilobytes.',
            'boolean' => 'Please select true or false for :attribute.',
            'array' => 'Please select multiple :attribute values.',
            
            // Custom messages for specific fields
            'property_details.city_view.required' => 'Please specify if there is a city view.',
            'property_details.city_view.string' => 'Please enter a valid city view description.',
            'property_details.family_villa.required' => 'Please specify if it is a family villa.',
            'property_details.family_villa.string' => 'Please enter a valid description for family villa.',
            'property_details.air_conditioned.required' => 'Please specify if the property is air conditioned.',
            'property_details.air_conditioned.string' => 'Please enter a valid description for air conditioned.',
            'property_details.phone.required' => 'Please enter a contact phone number.',
            'property_details.phone.numeric' => 'Please enter a valid phone number.',
            'property_details.phone.digits' => 'The phone number must be :digits digits.',
            'property_details.internet.required' => 'Please specify if the property has internet access.',
            'property_details.internet.string' => 'Please enter a valid description for internet access.',
        ]);



            if ($validator->fails()) {
                // If validation fails, return back with errors
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            // dd(session());

            try {
                $validatedData = $validator->validated();

                // Convert specific fields to required data types
                $validatedData['bedrooms'] = intval($validatedData['bedrooms']);
                $validatedData['bathrooms'] = intval($validatedData['bathrooms']);
                $validatedData['parking'] = intval($validatedData['parking']);
                $validatedData['dealer_contact'] = intval($validatedData['dealer_contact']);
                $validatedData['isAvailable'] = isset($validatedData['isAvailable']) ? boolval($validatedData['isAvailable']) : false;
                
                $user_id =(string) Session::get('user')['_id'];
                $validatedData['user_id'] = $user_id;

                // Handle photo upload
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $filename = time() . '_' . $photo->hashName();
                    $photo->move(public_path('images/property_default_image'), $filename);
                    $validatedData['photo'] = 'images/property_default_image/' . $filename;
                }

                // Handle multiple images upload
                if ($request->hasFile('images')) {
                    $multiple_photos = [];
                    foreach ($request->file('images') as $image) {
                        $filename = time() . '_' . $image->hashName();
                        $image->move(public_path('images/property_images'), $filename);
                        $multiple_photos[] = 'images/property_images/' . $filename;
                    }
                    $validatedData['images'] = $multiple_photos;
                }

                Property::create($validatedData);

                return redirect()->route('properties')->with('success', 'Property added successfully');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => $e->getMessage()]);
            }
        }



    

   

    public function delete($id)
    {
        $property = Property::findOrFail($id)->delete();

        if ($property) {
            session()->flash('success', 'Property Deleted Successfully');
            return redirect(route('properties'));
        } else {
            session()->flash('error', 'Property Not Deleted successfully');
            return redirect(route('properties'));
        }
    }



    public function edit($id)
    {
        $property = Property::findOrFail($id);
        return view('property.update', compact('property'));
    }
   

    public function update(Request $request, $id)
    {
      
      
        try {
            $property = Property::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'property_name' => 'required|string|min:4|max:40',
                'price' => 'required|numeric|min:0',
                'bedrooms' => 'required|integer|min:0',
                'bathrooms' => 'required|integer|min:0',
                'area_sqft' => 'required|numeric|min:0',
                'deal' => 'required|in:sale,rent',
                'type' => 'required|in:house,apartment,villa',
                'parking' => 'required|integer|min:0',
                'description' => 'required|string|min:4',
                'dealer_contact' => 'required|numeric|digits_between:10,10',
                'images.*' => 'nullable|max:2048',
                'photo' => 'nullable|max:2048',
                'assigned_buyer' => 'nullable|string',
                'isAvailable' => 'boolean',
                'dealer' => 'nullable|string',
                'district' => 'nullable|string',
                'property_details' => 'nullable|array',
                'property_details.city_view' => 'nullable|string',
                'property_details.family_villa' => 'nullable|string',
                'property_details.air_conditioned' => 'nullable|string',
                'property_details.phone' => 'nullable|integer|digits:10',
                'property_details.internet' => 'nullable|string',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
    
            $validatedData = $validator->validated();
    
             // Handle photo update
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($property->photo) {
                unlink(public_path($property->photo));
            }

            // Store new photo
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->hashName();
            $photo->move(public_path('images/property_default_image'), $filename);
            $validatedData['photo'] = 'images/property_default_image/' . $filename;
        } else {
            $validatedData['photo'] = $property->photo;
        }

        // Handle existing images deletion and update
        if ($request->has('delete_images')) {
            $delete_images = $request->input('delete_images');
            foreach ($delete_images as $imagePath) {
                if (file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }
                $validatedData['images'] = $property->images->filter(function ($image) use ($imagePath) {
                    return $image !== $imagePath;
                })->toArray();
            }
        } else {
            $validatedData['images'] = $property->images->toArray();
        }

        // Handle existing images update
        if ($request->has('existing_images')) {
            $existing_images = $request->input('existing_images');
            foreach ($existing_images as $index => $imagePath) {
                if ($request->hasFile("existing_images_file.{$index}")) {
                    $image = $request->file("existing_images_file.{$index}");
                    $filename = time() . '_' . $image->hashName();
                    $image->move(public_path('images/property_images'), $filename);
                    $existing_images[$index] = 'images/property_images/' . $filename;
                    if (file_exists(public_path($imagePath))) {
                        unlink(public_path($imagePath));
                    }
                }
            }
            $validatedData['images'] = array_merge($existing_images, $validatedData['images']);
        }

        // dd($request);
        // dd($id);
   // Handle multiple images update
if ($request->hasFile('images')) {
    $new_images = [];
    foreach ($request->file('images') as $image) {
        $filename = time() . '_' . $image->hashName();
        $image->move(public_path('images/property_images'), $filename);
        $new_images[] = 'images/property_images/' . $filename;
    }
    
    $existing_images = $validatedData['images'] ?? [];
    $validatedData['images'] = array_merge($new_images, $existing_images);
}


            // dd($property);

            $property->update($validatedData);
    
            if ($property) {
                session()->flash('success', 'Property Updated Successfully');
                return redirect(route('properties'));
            } else {
                session()->flash('error', 'Some problem occurred');
                return redirect(route('properties.update', $id));
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    
    
    public function showUserProperties($user_id, Request $request)
    {
        $user_id = $user_id;
    
        $user = User::find($user_id);
    
        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }
    
        $searchQuery = $request->input('search');
    
        $propertiesQuery = $user->properties();
        if ($searchQuery) {
            $propertiesQuery->where('property_name', 'like', '%' . $searchQuery . '%');
        }
        
        $properties = $propertiesQuery->paginate(10);
    
        return view('property.user_properties', compact('properties', 'searchQuery', 'user_id'));
    }
    
    
    
    
    
}
