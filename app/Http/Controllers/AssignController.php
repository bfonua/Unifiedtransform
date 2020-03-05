<?php

namespace App\Http\Controllers;

use App\User;
use App\Assign;
use Illuminate\Http\Request;
use App\Services\User\UserService;

// use App\Services\User\UserService;

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
        $classes = \App\Myclass::bySchool(\Auth::user()->school->id)
            ->get();
        $classeIds = \App\Myclass::bySchool(\Auth::user()->school->id)
            ->pluck('id')
            ->toArray();
        $sections = \App\Section::whereIn('class_id', $classeIds)
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        // $classAssign =  $classTotalAssign = $studentCount = $sectionPayment = $sectionRemain = $test = [];
        // foreach($classes as $class){
        //     $forms = $class->active_sections()->get();
        //     $assignTotal = 0;
        //     $assignAssign = [];
        //     foreach($forms as $form){
        //         $studentCount[$form->id] = $this->userService->getStudentCount($form->id, now()->year);
        //         $students = $this->userService->getAssignedStudentsID($form->id, now()->year);

        //         $minutes = 1440;// 24 hours = 1440 minutes
        //         $school_id = \Auth::user()->school->id;
        //         $getFees = \Cache::remember('totalFees'.$form->id.'-'.$school_id, $minutes, function () use($school_id, $students) {
        //             return $this->userService->getFeesStudents(now()->year, $students);
        //         });
        //         // return $getFees;
        //         $assignAssign[$form->id] = $getFees['assign'];
        //         $assignTotal += $getFees['assign'];
        //         $sectionPayment[$form->id] = $getFees['payment'];
        //         $sectionRemain[$form->id] = $getFees['remain'];
        //     }
        //     $classAssign[$class->class_number] = $assignAssign;
        //     $classTotalAssign[$class->class_number] = $assignTotal;
        // }
        // return $sectionPayment;
        return view('finance.assigned', [
            'classes' => $classes,
            'sections' => $sections,
            'school' => $school,
            // 'classAssign' => $classAssign,
            // 'classTotalAssign' => $classTotalAssign,
            // 'studentCount' => $studentCount,
            // 'sectionPayment' => $sectionPayment,
            // 'sectionRemain' => $sectionRemain,
        ]);
    }

    public function sectionFeeList(Request $request)
    {
        // return $request->id;
        $section_id = $request->id;
        $students = $this->userService->getTCTSectionStudentsWithSchool($section_id);
        $section = \App\Section::find($section_id);
        $feeTypesID = \App\Fee::where('session', now()->year)
            ->groupBy('fee_type_id')
            ->orderBy('fee_type_id', 'asc')
            ->pluck('fee_type_id')->toArray();
        $studentFees = [];
        $feeTypes = \App\FeeType::find($feeTypesID);

        foreach ($students as $student) {
            $assign = $payment = $remain = [];
            $assignTotal = $paymentTotal = $remainTotal = 0;
            foreach ($feeTypes as $type) {
                $feeAssign = \App\Fee::whereHas('assigns', function ($q) use ($student) {
                    $q->where('user_id', $student->id)
                    ->where('session', now()->year);
                })->where('fee_type_id', $type->id)
                ->first();

                $assign[$type->name] = $this->userService->numberformat($amountAssign = ($feeAssign) ? $feeAssign->amount : 0);
                $assignTotal += $amountAssign;

                $payment[$type->name] = $this->userService->numberformat($amountPaid = \App\Payment::whereHas('fees', function ($q) use ($type) {
                    $q->where('fee_type_id', $type->id);
                })->where('user_id', $student->id)
                ->where('session', now()->year)
                ->sum('amount'));
                $paymentTotal += $amountPaid;

                $remain[$type->name] = $this->userService->numberformat($amountRemain = $amountAssign - $amountPaid);
            }
            $assign['total'] = $this->userService->numberformat($assignTotal);
            $payment['total'] = $this->userService->numberformat($paymentTotal);
            $remain['total'] = $this->userService->numberformat($assignTotal - $paymentTotal);
            $studentFees[$student->id] = [
                'assign' => $assign,
                'payment' => $payment,
                'remain' => $remain,
            ];

            // return $studentFees;
        }

        // return $feeTypes;
        // $max_form = DB::table('student_infos')->where(['form_id'=> $section_id, 'session'=>now()->year])->max('form_num');
        // $max_loop = ($max_form == 0)? 1 : $max_form;

        return view('finance.section-tct-finance', compact('students', 'section', 'feeTypes', 'studentFees'));
    }

    public function showUnassigned()
    {
        $unassigned = \App\StudentInfo::where(
            [
                'session' => now()->year,
                'assigned' => 0,
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
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
                    $assign = new \App\Assign();
                    $assign->user_id = $request->user_id;
                    $assign->fee_id = $fee->id;
                    $assign->session = ($request->session) ? $request->session : now()->year;
                    $assign->save();
                }
            }
            if ($session > 2019) {
                $student = \App\User::find($request->user_id)->studentInfo;
                if (0 == $student->assigned) {
                    $student->assigned = 1;
                }
                $student->channel_id = $request->channel;
                $student->save();
            } else {
                // INSERT INTO REGTABLE details
            }
            if ('1' == $request->goAssign) {
                return redirect('fees/unassign');
            } else {
                return redirect('/user/'.\App\User::find($request->user_id)->student_code);
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
            if (isset($request['type']) and 0 != $request->channel) {
                // return $request;
                $firstRows = \App\Assign::where('user_id', $request->user_id)
                    ->where('session', $request->session)
                    ->delete();

                return $this->store($request);
            } else {
                return view('finance.assignForm', compact('user', 'session'));
            }
        } catch (\Exception $e) {
            Log::info('Failed to update Student information'.$e->getMessage());

            return view('finance.assignForm', compact('user', 'session'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Assign $assign
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Assign $assign)
    {
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
     * @param \App\Assign $assign
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Assign $assign)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Assign              $assign
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assign $assign)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Assign $assign
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assign $assign)
    {
    }
}
