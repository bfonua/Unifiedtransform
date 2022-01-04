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
        return $this->hasMany('App\User', 'section_id')->where('session', now()->year);
    }

    // public function students()
    // {
    //     return $this->hasManyThrough('App\User', 'App\StudentInfo','form_id', 'id', 'id', 'user_id')->where('session', now()->year);
    // }

    public function studentInfo()
    {
        return $this->hasMany('App\StudentInfo', 'form_id');
    }

    public function students()
    {
        return $this->hasManyDeep(
            'App\User', ['App\StudentInfo'],
            [
                'form_id', // Section FK on StudentInfo
                'id' // StudentInfo FK on User
            ],
            [
                'id', // LK on Section
                'student_id' // LK on StudentInfo
            ]
        )->where('session', now()->year);
    }

    public function assigned()
    {
        return $this->hasManyDeep(
            'App\Assign',
            ['App\StudentInfo', 'App\User'],
            [
                'form_id', // FK on StudentInfo
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

    public function totalAssignedAmount()
    {
        return $this->totalAssigned()
            ->selectRaw('sum(fees.amount) as aggregate')
            ->groupBy('student_infos.form_id');
    }

    public function getTotalAssignedAmountAttribute()
    {
        if (!$this->relationLoaded('totalAssignedAmount'))
            $this->load('totalAssignedAmount');
        $related = $this->getRelation('totalAssignedAmount');
        return ($related)? $related->first(): 0 ;
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

    public function totalPaidAmount(){
        return $this->payment()
            ->selectRaw('sum(payments.amount) as aggregate')
            ->groupBy('student_infos.form_id');
    }

    public function getTotalPaidAmountAttribute()
    {
        if(!$this->relationLoaded('totalPaidAmount'))
            $this->load('totalPaidAmount');
        $related = $this->getRelation('totalPaidAmount');
        return ($related)? $related->first() : 0 ;
    }

}
