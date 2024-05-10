<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'reviews';
    protected $guarded = [];
    protected $fillable = ['user_id', 'dealer_id', 'comment','status'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dealer()
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }
  
}
