<?php

namespace App;

class Account extends Model
{
    public function school()
    {
        return $this->belongsTo(\App\School::class, 'school_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
