<?php

namespace App\Http\Controllers;

use App\User;
use App\Myclass;
use App\Section;
use App\Department;

class SettingController extends Controller
{
    public function index()
    {
        $school = \Auth::user()->school;
        $classes = Myclass::all();
        $sections = Section::all();
        $departments = Department::bySchool(\Auth::user()->school_id)->get();
        $teachers = User::select('departments.*', 'users.*')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->where('role', 'teacher')
            ->orderBy('name', 'ASC')
            ->where('active', 1)
            ->get();

        return view('settings.index', compact('school', 'classes', 'sections', 'departments', 'teachers'));
    }
}
