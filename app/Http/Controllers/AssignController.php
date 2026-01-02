<?php

namespace App\Http\Controllers;

use App\Assign;
use Illuminate\Http\Request;
use App\Services\User\UserService;
use App\User;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AssignController extends Controller
{
    protected $userService;
    protected $user;

    public function __construct(UserService $userService, User $user)
    {
        $this->userService = $userService;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $school = \Auth::user()->school;
        $classes = \App\Myclass::bySchool(\Auth::user()->school->id)->get();
        $sections = \App\Section::with('class', 'totalAssignedAmount', 'totalPaidAmount')->withCount(['students' => function ($q) {
            $q->where('active', 1);
        }])
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        return view('finance.assigned', compact('classes', 'sections', 'school'));
    }

    /**
     * Display fees assigned for a specific year.
     *
     * @param  int  $year
     * @return \Illuminate\Http\Response
     */
    public function assignedByYear($year)
    {
        $school = \Auth::user()->school;
        $classes = \App\Myclass::bySchool(\Auth::user()->school->id)->get();
        
        // Get sections with fees assigned in the specified year
        $sections = \App\Section::with('class')
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();

        // For each section, get the year-specific totals and student counts
        foreach ($sections as $section) {
            // Get student count for the specified year from both student_infos and regrecords (including inactive)
            $fromStudentInfos = \App\User::whereHas('studentInfo', function($q) use ($section, $year) {
                $q->where('form_id', $section->id)->where('session', $year);
            })->pluck('id');
            
            $fromRegrecords = \App\User::whereHas('regrecord', function($q) use ($section, $year) {
                $q->where('form_id', $section->id)->where('session', $year);
            })->pluck('id');
            
            $section->students_count = $fromStudentInfos->merge($fromRegrecords)->unique()->count();
            
            // Get assigned and paid amounts
            $assignedResult = $section->totalAssignedForYear($year)->first();
            $paidResult = $section->totalPaidForYear($year)->first();
            
            $section->total_assigned_year = $assignedResult ? $assignedResult->aggregate : 0;
            $section->total_paid_year = $paidResult ? $paidResult->aggregate : 0;
        }

        return view('finance.assigned-by-year', compact('classes', 'sections', 'school', 'year'));
    }

    public function sectionFeeList(Request $request)
    {
        $students = $this->userService->getTCTSectionStudentsWithFinance($request->id);
        $section = \App\Section::find($request->id);
        $studentFees = [];
        $feeTypes = \App\FeeType::withCount(['fees' => function($q) {
                $q->where('session', now()->year);
        }])
            ->where('fee_types.active', 1)
            ->get();
        foreach ($students as $student) {
            $assign =  $payment = $remain = [];
            foreach ($feeTypes as $type) {
                $amountAssign = (!empty($student->fee_types_assigned->where('id', $type->id)->first()))? $student->fee_types_assigned->where('id', $type->id)->first()->aggregate : 0;
                $assign[$type->name] = $this->userService->numberformat($amountAssign);
                $amountPaid = (!empty($student->fee_types_paid->where('id', $type->id)->first()))? $student->fee_types_paid->where('id', $type->id)->first()->aggregate : 0;
                $payment[$type->name] = $this->userService->numberformat($amountPaid);
                $remain[$type->name] = $this->userService->numberformat($amountRemain = $amountAssign - $amountPaid);
            }
            $totalAssign = (!empty($student->totalFeesAssigned->first()))? $student->totalFeesAssigned->first()->aggregate : 0;
            $assign['total'] = $this->userService->numberformat($totalAssign);
            $totalPaid = (!empty($student->totalFeesPaid->first()))? $student->totalFeesPaid->first()->aggregate : 0;
            $payment['total'] = $this->userService->numberformat($totalPaid);
            $remain['total'] = $this->userService->numberformat($totalAssign - $totalPaid);
            $studentFees[$student->id] = [
                'assign' => $assign,
                'payment' => $payment,
                'remain' => $remain,
            ];
        }

        // Get all sections with student counts for navigation
        $sections = \App\Section::with('class')
            ->withCount('students')
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        
        $sectionsActive = \App\Section::withCount(['students' => function ($q) {
                $q->where('active', 1);
            }])
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        
        $studentCountList = [
            'total' => $sections->pluck('students_count', 'id')->toArray(),
            'active' => $sectionsActive->pluck('students_count', 'id')->toArray(),
        ];

        return view('finance.section-tct-finance', compact('students', 'section', 'feeTypes', 'studentFees', 'sections', 'studentCountList'));
    }

    public function sectionFeeListByYear($sectionId, $year)
    {
        $students = $this->userService->getTCTSectionStudentsWithFinanceByYear($sectionId, $year);
        $section = \App\Section::find($sectionId);
        $studentFees = [];
        
        // Get fee types filtered by the year
        $feeTypes = \App\FeeType::withCount(['fees' => function($q) use ($year) {
                $q->where('session', $year);
        }])
            ->where('fee_types.active', 1)
            ->get();
        
        // Get all student IDs at once for bulk queries
        $studentIds = $students->pluck('id')->toArray();
        
        // Bulk fetch all assignments for this year and these students
        $assignments = \App\Assign::with('fees.fee_type')
            ->where('session', $year)
            ->whereIn('user_id', $studentIds)
            ->get()
            ->groupBy('user_id');
        
        // Bulk fetch all payments for these students
        $allFeeIds = \App\Assign::where('session', $year)
            ->whereIn('user_id', $studentIds)
            ->pluck('fee_id')
            ->unique()
            ->toArray();
            
        $payments = \App\Payment::whereIn('user_id', $studentIds)
            ->whereIn('fee_id', $allFeeIds)
            ->get()
            ->groupBy('user_id');
        
        // Process each student using cached data
        foreach ($students as $student) {
            $assign = $payment = $remain = [];
            $studentAssignments = $assignments->get($student->id, collect());
            $studentPayments = $payments->get($student->id, collect());
            
            // Build a map of fee_id => payment amount for this student
            $paymentMap = $studentPayments->groupBy('fee_id')->map(function($group) {
                return $group->sum('amount');
            });
            
            // Calculate totals by fee type
            $totalAssign = 0;
            $totalPaid = 0;
            
            foreach ($feeTypes as $type) {
                // Filter assignments by fee type
                $typeAssignments = $studentAssignments->filter(function($assignment) use ($type) {
                    return $assignment->fees && $assignment->fees->fee_type_id == $type->id;
                });
                
                $amountAssign = $typeAssignments->sum('fees.amount');
                $assign[$type->name] = $this->userService->numberformat($amountAssign);
                
                // Calculate payments for this fee type
                $amountPaid = $typeAssignments->sum(function($assignment) use ($paymentMap) {
                    return $paymentMap->get($assignment->fee_id, 0);
                });
                $payment[$type->name] = $this->userService->numberformat($amountPaid);
                
                $remain[$type->name] = $this->userService->numberformat($amountAssign - $amountPaid);
                
                $totalAssign += $amountAssign;
                $totalPaid += $amountPaid;
            }
            
            $assign['total'] = $this->userService->numberformat($totalAssign);
            $payment['total'] = $this->userService->numberformat($totalPaid);
            $remain['total'] = $this->userService->numberformat($totalAssign - $totalPaid);
            
            $studentFees[$student->id] = [
                'assign' => $assign,
                'payment' => $payment,
                'remain' => $remain,
            ];
        }

        // Get all sections with student counts for navigation (optimized with single queries)
        $sections = \App\Section::with('class')
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        
        // Get total and active student counts in single queries using UNION
        $totalCounts = \DB::table('users')
            ->select('student_infos.form_id as section_id', \DB::raw('COUNT(DISTINCT users.id) as count'))
            ->join('student_infos', 'users.id', '=', 'student_infos.student_id')
            ->where('student_infos.session', $year)
            ->whereIn('student_infos.form_id', $sections->pluck('id'))
            ->groupBy('student_infos.form_id')
            ->get()
            ->keyBy('section_id');
            
        $totalCountsFromReg = \DB::table('users')
            ->select('regrecords.form_id as section_id', \DB::raw('COUNT(DISTINCT users.id) as count'))
            ->join('regrecords', 'users.id', '=', 'regrecords.user_id')
            ->where('regrecords.session', $year)
            ->whereIn('regrecords.form_id', $sections->pluck('id'))
            ->groupBy('regrecords.form_id')
            ->get()
            ->keyBy('section_id');
            
        $activeCounts = \DB::table('users')
            ->select('student_infos.form_id as section_id', \DB::raw('COUNT(DISTINCT users.id) as count'))
            ->join('student_infos', 'users.id', '=', 'student_infos.student_id')
            ->where('student_infos.session', $year)
            ->where('users.active', 1)
            ->whereIn('student_infos.form_id', $sections->pluck('id'))
            ->groupBy('student_infos.form_id')
            ->get()
            ->keyBy('section_id');
            
        $activeCountsFromReg = \DB::table('users')
            ->select('regrecords.form_id as section_id', \DB::raw('COUNT(DISTINCT users.id) as count'))
            ->join('regrecords', 'users.id', '=', 'regrecords.user_id')
            ->where('regrecords.session', $year)
            ->where('users.active', 1)
            ->whereIn('regrecords.form_id', $sections->pluck('id'))
            ->groupBy('regrecords.form_id')
            ->get()
            ->keyBy('section_id');
        
        // Merge counts
        $studentCountList = [
            'total' => [],
            'active' => [],
        ];
        
        foreach ($sections as $sec) {
            $totalFromInfo = $totalCounts->get($sec->id)->count ?? 0;
            $totalFromReg = $totalCountsFromReg->get($sec->id)->count ?? 0;
            $studentCountList['total'][$sec->id] = $totalFromInfo + $totalFromReg;
            
            $activeFromInfo = $activeCounts->get($sec->id)->count ?? 0;
            $activeFromReg = $activeCountsFromReg->get($sec->id)->count ?? 0;
            $studentCountList['active'][$sec->id] = $activeFromInfo + $activeFromReg;
        }

        return view('finance.section-tct-finance-by-year', compact('students', 'section', 'feeTypes', 'studentFees', 'sections', 'studentCountList', 'year'));
    }

    public function showUnassigned()
    {
        $unassigned = \App\StudentInfo::with('student','house', 'section.class')->where(
            [
                'session' => now()->year,
                'assigned' => 0
            ]
        )->get();
        return view('finance.unassigned', [
            'unassigned' => $unassigned,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $channel_id = $request->channel;
        $user = \App\User::find($request->user_id);
        $session = $request->session;
        if (isset($request['type'])) {
            foreach ($request['type'] as $fee_type_id => $toAssign) {
                if ($toAssign) {
                    $fee = \App\Fee::where('fee_channel_id', $channel_id)
                        ->where('fee_type_id', $fee_type_id)
                        ->first();
                    $assign = new \App\Assign;
                    $assign->user_id = $request->user_id;
                    $assign->fee_id = $fee->id;
                    $assign->session = ($request->session) ? $request->session : now()->year;
                    $assign->save();
                }
            }
            if ($session > 2019) {
                $student = \App\User::find($request->user_id)->studentInfo;
                if ($student->assigned == 0) {
                    $student->assigned = 1;
                }
                $student->channel_id = $request->channel;
                $student->save();
            } else {
                // INSERT INTO REGTABLE details
            }
            if ($request->goAssign == "1") {
                return redirect('fees/unassign');
            } else {
                return redirect('/user/' . \App\User::find($request->user_id)->student_code);
            }
        } else {
            return view('finance.assignForm', compact('user', 'session'));
        }
    }

    public function reassign(Request $request)
    {
        try {
            $user = \App\User::find($request->user_id);
            $session = $request->session;
            if (isset($request['type']) and $request->channel != 0) {
                // return $request;
                $firstRows = \App\Assign::where('user_id', $request->user_id)
                    ->where('session', $request->session)
                    ->delete();
                return $this->store($request);
            } else {
                return view('finance.assignForm', compact('user', 'session'));
            }
        } catch (\Exception $e) {
            Log::info('Failed to update Student information' . $e->getMessage());
            return view('finance.assignForm', compact('user', 'session'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Assign  $assign
     * @return \Illuminate\Http\Response
     */
    public function show(Assign $assign)
    {
        //
    }

    public function showForm(Request $request)
    {
        // return $request;
        $user = \App\User::find($request->user_id);
        $session = $request->session;
        $feeList = [];
        $fees_assigned = \App\Assign::with(['fees'])
            ->where('user_id', $user->id)
            ->where('session', $session)
            ->groupBy('fee_id')
            ->get();
        if ($fees_assigned->first()) {
            $feeList[$session]['year'] = $session;
            $feeIDs = $fees_assigned->pluck('fee_id')->toArray();
            $feeTypeIDs = \App\Fee::find($feeIDs)->pluck('fee_type_id')->toArray();
            $feeType = \App\FeeType::find($feeTypeIDs)->pluck('name')->toArray();
            $feeList[$session]['types'] = $feeType;
            $feeList[$session]['fee_id'] = $feeIDs;
        }
        $assigned = count($fees_assigned);
        return view('finance.assignForm', compact('user', 'session', 'feeList', 'assigned'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Assign  $assign
     * @return \Illuminate\Http\Response
     */
    public function edit(Assign $assign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Assign  $assign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assign $assign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Assign  $assign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assign $assign)
    {
        //
    }
}
