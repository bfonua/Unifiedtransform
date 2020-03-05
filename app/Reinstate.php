<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// use App\Inactive;
// use App\Users;

class Reinstate extends Model
{
    public function users()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function inactive()
    {
        return $this->belongsTo(\App\Inactive::class, 'inactive_id');
    }
}
