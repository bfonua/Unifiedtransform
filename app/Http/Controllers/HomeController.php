<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        if (\Auth::user()->role != 'master') {
            $minutes = 1440; // 24 hours = 1440 minutes
            $school_id = \Auth::user()->school->id;
            $classes = \Cache::remember('classes-' . $school_id, $minutes, function () use ($school_id) {
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

            $totalTeachers = \Cache::remember('totalTeachers-' . $school_id, $minutes, function () use ($school_id) {
                return \App\User::bySchool($school_id)
                    ->where('role', 'teacher')
                    ->where('active', 1)
                    ->count();
            });
            $totalBooks = \Cache::remember('totalBooks-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Book::bySchool($school_id)->count();
            });
            $totalClasses = \Cache::remember('totalClasses-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Myclass::bySchool($school_id)->count();
            });
            $totalSections = \Cache::remember('totalSections-' . $school_id, $minutes, function () use ($classes) {
                return \App\Section::whereIn('class_id', $classes)->count();
            });
            $notices = \Cache::remember('notices-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Notice::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $events = \Cache::remember('events-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Event::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $routines = \Cache::remember('routines-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Routine::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $syllabuses = \Cache::remember('syllabuses-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Syllabus::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $exams = \Cache::remember('exams-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Exam::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });

            // TCT functions
            $totalStudents = \App\User::whereHas("studentInfo", function ($q) {
                $q->where("session", now()->year);
            })->count();
            $totalActive = \App\User::whereHas("studentInfo", function ($q) {
                $q->where("session", now()->year);
            })->where("active", 1)
                ->count();
            $inactiveType = ["withdrawn", "removed", "suspended", "expelled"];
            $inactiveOutput = [];
            foreach ($inactiveType as $type) {
                $inactive = \App\Inactive::where('session', now()->year)
                    ->where('type', $type)
                    ->distinct('user_id')
                    ->count();
                $inactiveOutput[$type] = $inactive;
            }

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
                $studentCountList['total'][$section->id] = $studentCount;

                $studentCountActive = \App\User::whereHas("studentInfo", function ($q) use ($section) {
                    $q->where("session", now()->year)
                        ->where('form_id', $section->id);
                })->where("active", 1)
                    ->count();
                $studentCountList['active'][$section->id] = $studentCountActive;
            }

            $studentCountHouse = [];
            foreach ($houses as $house) {
                $studentCount = \App\StudentInfo::where('house_id', $house->id)
                    ->where('session', now()->year)
                    ->count('id');
                $studentCountHouse[$house->id] = $studentCount;
            }
            $feeArr = $feeAss = $feePay = $feeRemain = [];
            for ($i = 1; $i <= 5; $i++) {
                $feeName = \App\FeeType::find($i)->name;
                $totalAssign = \DB::table('assigns')
                    ->join('fees', 'assigns.fee_id', '=', 'fees.id')
                    ->where('assigns.session', now()->year)
                    ->where('fees.fee_type_id', $i)
                    ->sum('fees.amount');
                $feeAss[$feeName] = $totalAssign;


                $totalPay = \DB::table('payments')
                    ->join('fees', 'payments.fee_id', '=', 'fees.id')
                    ->where('payments.session', now()->year)
                    ->where('fees.fee_type_id', $i)
                    ->sum('payments.amount');
                $feePay[$feeName] = $totalPay;

                $feeRemain[$feeName] = $totalAssign - $totalPay;
            }
            $feeAss['total'] = array_sum($feeAss);
            $feePay['total'] = array_sum($feePay);
            $feeRemain['total'] = $feeAss['total'] - $feePay['total'];
            $feeArr = [
                'Assigned' => $feeAss,
                'Payment' => $feePay,
                'Remain' => $feeRemain,
            ];
            // return $feeArr;

            // if(\Auth::user()->role == 'student')
            //   $messageCount = \App\Notification::where('student_id',\Auth::user()->id)->count();
            // else
            //   $messageCount = 0;
            return view('home', [
                'totalStudents' => $totalStudents,
                'totalActive' => $totalActive,
                'inactiveOutput' => $inactiveOutput,
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
                'feeArr' => $feeArr,
                //'messageCount'=>$messageCount,
            ]);
        } else {
            return redirect('/masters');
        }
    }
}
