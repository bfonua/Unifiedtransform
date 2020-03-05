<?php

namespace App;

class StudentBoardExam extends Model
{
    protected $table = 'student_board_exams';

    /**
     * Get the student record associated with the user.
     */
    public function student()
    {
        return $this->belongsTo(\App\User::class);
    }
}
