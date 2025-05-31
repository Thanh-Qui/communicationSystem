<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Chatify\Traits\UUID;

class ChMessage extends Model
{
    use UUID;

    protected $casts = [
        'seen' => 'array'
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function usersInChannel()
    {
        return $this->belongsToMany(User::class, 'ch_channel_user', 'channel_id', 'user_id', 'to_channel_id', 'id');
    }
}
