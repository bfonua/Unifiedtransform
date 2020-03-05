<?php

namespace App;

class Attendance extends Model
{
    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * Get the section record associated with the attendance.
     */
    public function section()
    {
        return $this->belongsTo(\App\Section::class);
    }

    /**
     * Get the exam record associated with the attendance.
     */
    public function exam()
    {
        return $this->belongsTo(\App\Exam::class);
    }
}
