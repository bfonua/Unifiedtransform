<?php

namespace App;

class Gradesystem extends Model
{
    protected $table = 'grade_systems';

    public function school()
    {
        return $this->belongsTo(\App\School::class);
    }
}
