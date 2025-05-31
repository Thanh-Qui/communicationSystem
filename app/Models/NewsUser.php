<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsUser extends Model
{
    protected $fillable = [
        'id_user',
        'id_news',
    ];
}
