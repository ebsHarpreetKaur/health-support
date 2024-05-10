<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
// use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Laravel\Sanctum\HasApiTokens;
use Jenssegers\Mongodb\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject 
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $guarded = [];

    
    protected $fillable = [
        'rera_number',
        'user_location',
        'mobile',
        'user_city',
        'name',
        'email',
        'user_pincode',
        'image',
        'password',
        'otp_status',
        'payment_res',
        'payment_status',
        'role',
        'status',
        'average_user_rating',
        'ratings',
        'properties'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'mobile'=>'integer',
        
        // 'user_location' => 'array',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    
    public function properties()
    {
        return $this->hasMany(Property::class );
    }



    public function senderMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }


    public function recipientMessages()
    {
        return $this->hasMany(Chat::class, 'recipient_id');
    }

    
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'dealer_id');
    }


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}