<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom');
    }

    public function books()
    {
        return $this->hasMany('App\Models\Book', 'subject_id', 'id');
    }
}
