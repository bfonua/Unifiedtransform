<?php

namespace App;

use App\Model;


class Section extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_number', 'room_number', 'class_id', 'user_id',
    ];
    /**
     * Get the class record associated with the user.
    */
    public function class()
    {
        return $this->belongsTo('App\Myclass');
    }

    public function users()
    {
        return $this->hasMany('App\User', 'section_id');
    }

    // public function students()
    // {
    //     return $this->hasManyThrough('App\User', 'App\StudentInfo','form_id', 'id', 'id', 'user_id')->where('session', now()->year);
    // }

    public function students()
    {
        return $this->hasManyDeep(
            'App\User', ['App\StudentInfo'],
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

    public function assigned()
    {
        return $this->hasManyDeep(
            'App\Assign',
            ['App\StudentInfo', 'App\User'],
            [
                'form_id', // FM on StudentInfo
                'id', // FK on User
                'user_id', // FK on Assign
            ],
            [
                'id', // LK on Section
                'student_id', // LK on StudentInfo
                'id', // LK on User
            ]
        )->where('assigns.session', now()->year);
    }

    public function totalAssigned()
    {
        return $this->hasManyDeep(
            'App\Fee',
            ['App\StudentInfo', 'App\User', 'App\Assign'],
            [
                'form_id', // FM on StudentInfo
                'id', // FK on User
                'user_id', // FK on Assign
                'id', //FK on Fee
            ],
            [
                'id', // LK on Section
                'student_id', // LK on StudentInfo
                'id', // LK on User
                'fee_id' // LK on Assign
            ]
        )->where('assigns.session', now()->year);
    }

    public function payment(){
        return $this->hasManyDeep(
            'App\Payment',
            ['App\StudentInfo', 'App\User'],
            [
                'form_id', // FM on StudentInfo
                'id', // FK on User
                'user_id', // FK on Payment
            ],
            [
                'id', // LK on Section
                'student_id', // LK on StudentInfo
                'id', // LK on User
            ]
        )->where('payments.session', now()->year);
    }


}
