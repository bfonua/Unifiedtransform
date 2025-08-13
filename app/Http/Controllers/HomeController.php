<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        if (Auth::user()->role != 'master') {
            $minutes = 120; // 24 hours = 1440 minutes
            $school_id = Auth::user()->school->id;
            $classes = Cache::remember('classes-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Myclass::bySchool($school_id)
                    ->pluck('id')
                    ->toArray();
            });
                 $totalTeachers = Cache::remember('totalTeachers-' . $school_id, $minutes, function () use ($school_id) {
                return \App\User::bySchool($school_id)
                    ->where('role', 'teacher')
                    ->where('active', 1)
                    ->count();
            });
            $totalBooks = Cache::remember('totalBooks-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Book::bySchool($school_id)->count();
            });
            $totalClasses = Cache::remember('totalClasses-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Myclass::bySchool($school_id)->count();
            });
            $totalSections = Cache::remember('totalSections-' . $school_id, $minutes, function () use ($classes) {
                return \App\Section::whereIn('class_id', $classes)->count();
            });
            $notices = Cache::remember('notices-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Notice::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $events = Cache::remember('events-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Event::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $routines = Cache::remember('routines-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Routine::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $syllabuses = Cache::remember('syllabuses-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Syllabus::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });
            $exams = Cache::remember('exams-' . $school_id, $minutes, function () use ($school_id) {
                return \App\Exam::bySchool($school_id)
                    ->where('active', 1)
                    ->get();
            });

            // TCT functions

            // Cache studentQuery and related counts
            $studentQuery = Cache::remember('studentQuery-' . $school_id, $minutes, function () {
                return \App\User::withCount('studentInfo')->whereHas("studentInfo", function ($q) {
                    $q->where("session", now()->year);
                })->get();
            });
            $totalStudents = $studentQuery->count();
            $totalActive = $studentQuery->where('active', 1)->count();

            // Cache inactiveOutput
            $inactiveOutput = Cache::remember('inactiveOutput-' . $school_id, $minutes, function () {
                $inactiveType = ["withdrawn", "removed", "suspended", "expelled"];
                $output = [];
                foreach ($inactiveType as $type) {
                    $inactive = \App\Inactive::where('session', now()->year)
                        ->where('type', $type)
                        ->distinct('user_id')
                        ->count();
                    $output[$type] = $inactive;
                }
                return $output;
            });

            // Cache sections and sectionsActive
            $sections = Cache::remember('sections-' . $school_id, $minutes, function () {
                return \App\Section::with('class')->withCount('students')
                    ->where('active', 1)
                    ->orderBy('class_id')
                    ->orderBy('section_number', 'asc')
                    ->get();
            });
            $sectionsActive = Cache::remember('sectionsActive-' . $school_id, $minutes, function () {
                return \App\Section::withCount(['students' => function ($q) {
                    $q->where('active', 1);
                }])
                    ->where('active', 1)
                    ->orderBy('class_id')
                    ->orderBy('section_number', 'asc')
                    ->get();
            });
            // Build studentCountList - associative array with section IDs to student counts
            $studentCountList = [
                'total' => $sections->pluck('students_count', 'id')->toArray(),
                'active' => $sectionsActive->pluck('students_count', 'id')->toArray(),
            ];

            // Cache houses and housesCount
            $houses = Cache::remember('houses-' . $school_id, $minutes, function () {
                return \App\House::withCount(['users' => function ($q) {
                    $q->where('active', 1);
                }, 'students'])
                    ->where('active', 1)
                    ->get();
            });
            $housesCount = $houses->count();

            // Build studentCountHouse - associative array with house IDs to student counts
            $studentCountHouse = $houses->pluck('users_count', 'id')->toArray();

            // Get the current session year
            $session = now()->year;

            // Get all assigns and payments for the session, eager load fees
            $assigns = DB::table('assigns')
                ->join('fees', 'assigns.fee_id', '=', 'fees.id')
                ->where('assigns.session', $session)
                ->select('fees.fee_type_id', 'fees.amount')
                ->get();
            $payments = DB::table('payments')
                ->join('fees', 'payments.fee_id', '=', 'fees.id')
                ->where('payments.session', $session)
                ->select('fees.fee_type_id', 'payments.amount')
                ->get();
            $feeTypes = \App\FeeType::where('active', 1)->get();
            $feeAss = $feePay = $feeRemain = [];
            foreach ($feeTypes as $feeType) {
                $assigned = $assigns->where('fee_type_id', $feeType->id)->sum('amount');
                $paid = $payments->where('fee_type_id', $feeType->id)->sum('amount');
                $feeAss[$feeType->name] = $assigned;
                $feePay[$feeType->name] = $paid;
                $feeRemain[$feeType->name] = $assigned - $paid;
            }
            $feeAss['total'] = array_sum($feeAss);
            $feePay['total'] = array_sum($feePay);
            $feeRemain['total'] = $feeAss['total'] - $feePay['total'];
            $feeArr = [
                'Assigned' => $feeAss,
                'Payment' => $feePay,
                'Remain' => $feeRemain,
            ];


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
                // 'classIDs' => $classIDs,
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
