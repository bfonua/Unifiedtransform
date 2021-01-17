<?php

namespace App;

use App\Model;

class Myclass extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;


    protected $table = "classes";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_number', 'group', 'school_id',
    ];
    /**
     * Get the school record associated with the user.
     */
    public function school()
    {
        return $this->belongsTo('App\School');
    }

    public function sections()
    {
        return $this->hasMany('App\Section', 'class_id')->orderBy('section_number');
    }

    public function active_sections()
    {
        return $this->hasMany('App\Section', 'class_id')->where('active', 1)->orderBy('section_number');
    }

    // public function exam()
    // {
    //     return $this->belongsTo('App\ExamForClass');
    // }

    public function books()
    {
        return $this->hasMany('App\Book', 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany('App\SubjectClass', 'class_id');
    }

    public function students()
    {
        return $this->hasManyDeep(
            'App\SubjectAssign',
            ['App\Section', 'App\StudentInfo'],
            [
                'class_id',
                'form_id',
                'user_id'
            ],
            [
                'id',
                'id',
                'student_id'
            ]
        )->where('student_infos.session', now()->year);
    }
}
