<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Payment extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'payment';
    protected $guarded = [];

    
    protected $fillable = [
        'amount',
        'currency',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'card_cvv',
        'billing_address',
        'description',
        'metadata',
        'payment_status',
        'date',
        'user_id',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


