<?php

namespace App;

class Department extends Model
{
    public function teachers()
    {
        return $this->hasMany('App\User', 'department_id');
    }
}
