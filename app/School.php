<?php

namespace App;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'about', 'medium', 'code', 'theme',
    ];

    public function users()
    {
        return $this->hasMany(\App\User::class);
    }

    public function departments()
    {
        return $this->hasMany(\App\Department::class);
    }

    public function gradesystems()
    {
        return $this->hasMany(\App\Gradesystem::class);
    }
}
