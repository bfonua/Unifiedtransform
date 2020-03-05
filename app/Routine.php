<?php

namespace App;

class Routine extends Model
{
    /**
     * Get the school record associated with the user.
     */
    public function school()
    {
        return $this->belongsTo(\App\School::class);
    }

    /**
     * Get the Section record associated with the Routine.
     */
    public function section()
    {
        return $this->belongsTo(\App\Section::class);
    }
}
