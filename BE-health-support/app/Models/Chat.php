<?php

namespace App\Models;

// use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;


class Chat extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'chats';
    protected $guarded = [];

    protected $fillable = [
        'sender_id', 'recipient_id', 'message', 'sent_at','status'
    ];
    protected $casts = [
        // 'message' => 'array',
    ];
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
