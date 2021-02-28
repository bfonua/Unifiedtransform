<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectAssign extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $fillable = [
        'user_id',
        'subject_id',
        'class_id',
        'session',
        'active',
    ];


    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject_id');
    }

    public function students()
    {
        return $this->hasManyDeep(
            'App\User',
            ['App\StudentInfo'],
            [
                'form_id',
                'id'
            ],
            [
                'id',
                'student_id'
            ]
        )->where('session', now()->year);
    }
}
