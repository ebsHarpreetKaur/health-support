<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use MongoDB\Laravel\Eloquent\Model;
// use Jenssegers\Mongodb\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'properties';
    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'dealer_contact',
        'location',
        'bedrooms',
        'bathrooms',
        'area_sqft',
        'property_name',
        'deal',
        'type',
        'parking',
        'description',
        'assigned_buyer',
        'isAvailable',
        'property_details',
        'dealer',
        'district',
        'price',
        'photo',
        'images',
        'status',
    ];

    // $property = Property::find($propertyId);

    // // Get the current property details
    // $propertyDetails = $property->property_details;
    
    // // Add more values to the property details array
    // $propertyDetails['additional_key'] = 'additional_value';
    // $propertyDetails['another_key'] = 'another_value';
    
    // // Update the property with the modified property details
    // $property->update([
    //     'property_details' => $propertyDetails,
    // ]);
    protected $casts = [
        'location' => 'array', // Assuming 'location' field is JSON
        // 'property_details' => 'array', // Assuming 'property_details' field is JSON
        // 'images' => 'array', // Assuming 'images' field is JSON
        // 'photo' => 'array', // Assuming 'photo' field is JSON
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

     public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
