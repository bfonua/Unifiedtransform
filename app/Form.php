<?php

namespace App;

class Form extends Model
{
    /**
     * Get the school record associated with the user.
     */
    public function school()
    {
        return $this->belongsTo('App\School');
    }
}
