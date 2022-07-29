<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    
    public function exercises()
    {
        return $this->hasMany('App\Models\Exercise', 'video_id', 'id');
    }
}
