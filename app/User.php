<?php

namespace App;

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

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasApiTokens;
    use Notifiable;
    use Impersonate;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'code', /* school code*/'student_code', 'active', 'verified', 'school_id', 'section_id', 'address', 'about', 'phone_number', 'blood_group', 'nationality', 'gender', 'department_id',
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
        return $this->belongsTo(\App\Section::class);
    }

    public function school()
    {
        return $this->belongsTo(\App\School::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Department::class, 'department_id', 'id');
    }

    public function studentInfo()
    {
        return $this->hasOne(\App\StudentInfo::class, 'student_id');
    }

    public function inactive()
    {
        return $this->hasMany(\App\Inactive::class, 'user_id');
    }

    public function inactiveNow($session)
    {
        return $this->hasMany(\App\Inactive::class, 'user_id')->where('session', $session);
    }

    public function reinstate()
    {
        return $this->hasMany(\App\Reinstate::class, 'user_id');
    }

    public function regrecord()
    {
        return $this->hasMany(\App\Regrecord::class, 'user_id');
    }

    public function studentBoardExam()
    {
        return $this->hasMany(\App\StudentBoardExam::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Notification::class, 'student_id');
    }

    public function feesAssigned()
    {
        return $this->hasMany(\App\Assign::class, 'user_id');
    }

    public function hasRole(string $role): bool
    {
        return $this->role == $role ? true : false;
    }
}
