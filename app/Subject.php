<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public function assigns()
    {
        return $this->hasMany('App\SubjectAssign');
    }

    public function class()
    {
        return $this->hasMany('\App\SubjectClass');
    }
}
