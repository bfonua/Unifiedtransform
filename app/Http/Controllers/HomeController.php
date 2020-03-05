<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ('master' != \Auth::user()->role) {
            $minutes = 1440; // 24 hours = 1440 minutes
            $school_id = \Auth::user()->school->id;
            $classes = \Cache::remember('classes-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Myclass::bySchool($school_id)
                ->pluck('id')
                ->toArray();
            });
            // $totalStudents = \Cache::remember('totalStudents-'.$school_id, $minutes, function () use($school_id) {
            //   return \App\User::bySchool($school_id)
            //     ->where('role','student')
            // ->studentInfo()
            // ->where('session', now()->year)
            // ->where('active', 1)
            //     ->count();
            // });

            $totalTeachers = \Cache::remember('totalTeachers-'.$school_id, $minutes, function () use ($school_id) {
                return \App\User::bySchool($school_id)
                              ->where('role', 'teacher')
                              ->where('active', 1)
                              ->count();
            });
            $totalBooks = \Cache::remember('totalBooks-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Book::bySchool($school_id)->count();
            });
            $totalClasses = \Cache::remember('totalClasses-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Myclass::bySchool($school_id)->count();
            });
            $totalSections = \Cache::remember('totalSections-'.$school_id, $minutes, function () use ($classes) {
                return \App\Section::whereIn('class_id', $classes)->count();
            });
            $notices = \Cache::remember('notices-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Notice::bySchool($school_id)
                                ->where('active', 1)
                                ->get();
            });
            $events = \Cache::remember('events-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Event::bySchool($school_id)
                              ->where('active', 1)
                              ->get();
            });
            $routines = \Cache::remember('routines-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Routine::bySchool($school_id)
                                ->where('active', 1)
                                ->get();
            });
            $syllabuses = \Cache::remember('syllabuses-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Syllabus::bySchool($school_id)
                                  ->where('active', 1)
                                  ->get();
            });
            $exams = \Cache::remember('exams-'.$school_id, $minutes, function () use ($school_id) {
                return \App\Exam::bySchool($school_id)
                              ->where('active', 1)
                              ->get();
            });

            // TCT functions
            $totalStudents = \App\User::whereHas('studentInfo', function ($q) {
                $q->where('session', now()->year);
            })->count();
            $classes = \App\Myclass::bySchool(\Auth::user()->school->id)
                ->get();
            $classIDs = \App\Myclass::bySchool(\Auth::user()->school->id)
                ->pluck('id')
                ->toArray();
            $sections = \App\Section::whereIn('class_id', $classIDs)
                ->where('active', 1)
                ->orderBy('class_id')
                ->orderBy('section_number', 'asc')
                ->get();
            $houses = \App\House::where('active', 1)
                ->get();
            $housesCount = \App\House::where('active', 1)->count();

            $studentCountList = [];
            foreach ($sections as $section) {
                $studentCount = \App\StudentInfo::where('form_id', $section->id)
                ->where('session', now()->year)
                ->count('id');
                $studentCountList[$section->id] = $studentCount;
            }

            $studentCountHouse = [];
            foreach ($houses as $house) {
                $studentCount = \App\StudentInfo::where('house_id', $house->id)
                    ->where('session', now()->year)
                    ->count('id');
                $studentCountHouse[$house->id] = $studentCount;
            }

            // if(\Auth::user()->role == 'student')
            //   $messageCount = \App\Notification::where('student_id',\Auth::user()->id)->count();
            // else
            //   $messageCount = 0;
            return view('home', [
              'totalStudents' => $totalStudents,
              'totalTeachers' => $totalTeachers,
              'totalBooks' => $totalBooks,
              'totalClasses' => $totalClasses,
              'totalSections' => $totalSections,
              'notices' => $notices,
              'events' => $events,
              'routines' => $routines,
              'syllabuses' => $syllabuses,
              'exams' => $exams,
              'classes' => $classes,
              'classIDs' => $classIDs,
              'sections' => $sections,
              'houses' => $houses,
              'housesCount' => $housesCount,
              'studentCountList' => $studentCountList,
              'studentCountHouse' => $studentCountHouse,
              //'messageCount'=>$messageCount,
            ]);
        } else {
            return redirect('/masters');
        }
    }
}
