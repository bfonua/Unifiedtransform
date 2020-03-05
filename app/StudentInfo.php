<?php

namespace App;

class StudentInfo extends Model
{
    protected $table = 'student_infos';
    protected $fillable = ['student_id'];

    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function house()
    {
        return $this->belongsTo(\App\House::class);
    }

    public function section()
    {
        return $this->belongsTo(\App\Section::class, 'form_id');
    }

    public function channel()
    {
        return $this->belongsTo(\App\FeeChannel::class, 'channel_id');
    }
}
