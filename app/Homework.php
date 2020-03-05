<?php

namespace App;

class Homework extends Model
{
    /**
     * Get the teacher record associated with the user.
     */
    public function teacher()
    {
        return $this->belongsTo(\App\User::class);
    }
}
