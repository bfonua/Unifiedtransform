<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectClass extends Model
{
    protected $fillable = [
        'subject_id',
        'user_id',
        'option',
        'session',
    ];

    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject_id');
    }

    public function class()
    {
        return $this->belongsTo('App\Myclass', 'class_id');
    }
}
