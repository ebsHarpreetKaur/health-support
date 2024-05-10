<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;


class Image extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'images';
    protected $fillable = ['images'];
    // protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }



    // $property = Property::create([
    //     'title' => 'Sample Property',
    //     // Other property attributes
    // ]);
    
    // $image = Image::create([
    //     'property_id' => $property->id,
    //     'image_url' => 'path_to_image',
    //     // Other image attributes
    // ]);
    
}
