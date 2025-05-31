<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'status',
    ];

    // quan hệ cha con trong thư mục
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
}
