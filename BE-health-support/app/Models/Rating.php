<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Jenssegers\Mongodb\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'ratings';
    protected $guarded = [];

    protected $fillable = ['user_id', 'dealer_id', 'rating','status',];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dealer()
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

   
}
