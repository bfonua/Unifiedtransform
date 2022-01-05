<?php

namespace App;

use App\Model;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, HasApiTokens, Notifiable, Impersonate, Billable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'code',/* school code*/'student_code', 'active', 'verified', 'school_id', 'section_id', 'address', 'about', 'phone_number', 'blood_group', 'nationality', 'gender', 'department_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeStudent($q)
    {
        return $q->where('role', 'student');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function school()
    {
        return $this->belongsTo('App\School');
    }

    public function department()
    {
        return $this->belongsTo('App\Department','department_id', 'id');
    }

    public function studentInfo(){
        return $this->hasOne('App\StudentInfo','student_id');
    }

    public function inactive(){
        return $this->hasMany('App\Inactive', 'user_id');
    }

    public function inactiveNow($session){
        return $this->hasMany('App\Inactive', 'user_id')->where('session', $session);
    }

    public function reinstate(){
        return $this->hasMany('App\Reinstate', 'user_id');
    }

    public function regrecord(){
        return $this->hasMany('App\Regrecord', 'user_id');
    }

    public function studentBoardExam(){
        return $this->hasMany('App\StudentBoardExam','student_id');
    }

    public function notifications(){
        return $this->hasMany('App\Notification','student_id');
    }

    public function feesAssigned(){
        return $this->hasMany('App\Assign', 'user_id');
    }

    public function subjectAssigned(){
        return $this->hasMany('App\SubjectAssign', 'user_id')->where('session', now()->year);
    }

    public function getFeesAssignedAttribute()
    {
        if (!$this->relationLoaded('feesAssigned'))
            $this->load('feesAssigned');
        $related = $this->getRelation('feesAssigned');
        return ($related)? $related: 0 ;
    }

    public function fees()
    {
         return $this->hasManyDeep(
            'App\Fee', ['App\Assign'],
            [
                'user_id', // User FK on Assign
                'id', // Assign FK on Fee
            ], 
            [
                'id', // LK on User
                'fee_id' // LK on Assign
            ]
        )->where('assigns.session', now()->year);
    }

    public function feeTypesAssigned()
    {
        return $this->hasManyDeep(
            'App\FeeType', ['App\Assign','App\Fee'],
            [
                'user_id', // User FK on Assign
                'id', // Assign FK on Fee
                'id' // Fee FK on FeeType
            ],
            [
                'id', // LK on User
                'fee_id', // LK on Assign
                'fee_type_id' // LK on Fee
            ]
        )->where('assigns.session', now()->year)
        ->where('fee_types.active', 1)
        ->selectRaw('fee_types.id, sum(fees.amount) as aggregate')
        ->groupBy('assigns.fee_id', 'assigns.user_id');
    }

    public function getFeeTypesAssignedAttribute()
    {
        if (!$this->relationLoaded('feeTypesAssigned'))
            $this->load('feeTypesAssigned');
        $related = $this->getRelation('feeTypesAssigned');
        return ($related)? $related: 0 ;
    }


    public function feeTypesPaid()
    {
        return $this->hasManyDeep(
            'App\FeeType', ['App\Payment', 'App\Fee'],
            [
                'user_id', // User FK on Payment
                'id', // Payment FK on Fee
                'id' // Fee FK on FeeType
            ],
            [
                'id', // LK on User
                'fee_id', // LK on Payment
                'fee_type_id' // LK on FeeType
            ]
        )->where('payments.session', now()->year)
        ->where('fee_types.active', 1)
        ->selectRaw('fee_types.id, sum(fees.amount) as aggregate')
        ->groupBy('payments.fee_id', 'payments.user_id');
    }

    public function getFeeTypesPaidAttribute()
    {
        if (!$this->relationLoaded('feeTypesPaid'))
            $this->load('feeTypesPaid');
        $related = $this->getRelation('feeTypesPaid');
        return ($related)? $related: 0 ;
    }

    public function totalFeesAssigned()
    {
         return $this->hasManyDeep(
            'App\Fee', ['App\Assign'],
            [
                'user_id', // User FK on Assign
                'id', // Assign FK on Fee
            ], 
            [
                'id', // LK on User
                'fee_id' // LK on Assign
            ]
        )->where('assigns.session', now()->year)
        ->selectRaw('sum(fees.amount) as aggregate')
        ->groupBy('assigns.user_id');
    }

    public function getTotalFeesAssignedAttribute()
    {
        if (!$this->relationLoaded('totalFeesAssigned'))
            $this->load('totalFeesAssigned');
        $related = $this->getRelation('totalFeesAssigned');
        return ($related)? $related: 0 ;
    }

    public function totalFeesPaid()
    {
        return $this->hasManyDeep(
            'App\Fee', ['App\Payment'],
            [
                'user_id', // User FK on Payment
                'id' // Payment FK on Fee
            ],
            [
                'id', // LK on User
                'fee_id' // LK on Payment
            ]
        )->where('payments.session', now()->year)
        ->selectRaw('sum(fees.amount) as aggregate')
        ->groupBy('payments.user_id');
    }
    
    public function getTotalFeesPaidAttribute()
    {
        if(!$this->relationLoaded('totalFeesPaid'))
            $this->load('totalFeesPaid');
        $related = $this->getRelation('totalFeesPaid');
        return ($related)? $related: 0;
    }

    public function hasRole(string $role): bool
    {
        return $this->role == $role ? true : false;
    }
}
