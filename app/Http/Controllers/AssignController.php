<?php

namespace App\Http\Controllers;

use App\Assign;
use Illuminate\Http\Request;
use App\Services\User\UserService;
use App\User;
// use App\Services\User\UserService;

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
            
        // return $sections->where('id', '38')->first()->total_paid_amount->aggregate;

        return view('finance.assigned', compact('classes', 'sections', 'school'));
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
        return view('finance.section-tct-finance', compact('students', 'section', 'feeTypes', 'studentFees'));
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
