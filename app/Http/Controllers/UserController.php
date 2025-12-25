<?php

namespace App\Http\Controllers;

use App\Department;
use App\Myclass;
use App\Section;
use App\StudentInfo;
use App\User;
use App\House;
use App\Regrecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\TCTCreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\CreateAdminRequest;
use App\Http\Requests\User\CreateTeacherRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ImpersonateUserRequest;
use App\Http\Requests\User\CreateLibrarianRequest;
use App\Http\Requests\User\CreateAccountantRequest;
use Mavinoo\Batch\Batch;
use App\Events\UserRegistered;
use App\Events\StudentInfoUpdateRequested;
use App\Events\TCTStudentInfoUpdateRequested;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\User\UserService;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
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
     * @param $school_code
     * @param $student_code
     * @param $teacher_code
     * @return \Illuminate\Http\Response
     */
    public function index($school_code, $student_code, $teacher_code)
    {
        session()->forget('section-attendance');

        if ($this->userService->isListOfStudents($school_code, $student_code))
            return $this->userService->indexView('list.student-list', $this->userService->getStudents());
        else if ($this->userService->isListOfTeachers($school_code, $teacher_code))
            return $this->userService->indexView('list.teacher-list', $this->userService->getTeachers());
        else
            return view('home');
    }

    public function tct_index($school_code, $student_code, $teacher_code)
    {
        return $this->userService->indexTCTView('list.tct-student-list', $this->userService->getTCTStudents(), 'registered');
    }

    public function tct_list_archive()
    {
        return $this->userService->indexTCTView('list.tct-student-list', $this->userService->getTCTArchive(), 'archived');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToRegisterStudent()
    {
        $classes = Myclass::query()
            ->bySchool(\Auth::user()->school->id)
            ->pluck('id');

        $sections = Section::with('class')
            ->whereIn('class_id', $classes)
            ->get();

        session([
            'register_role' => 'student',
            'register_sections' => $sections,
        ]);
        return redirect()->route('register');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    // Update controller to redirect to TCT version of Registration Form
    public function redirectToRegisterTCTStudent()
    {
        $classes = Myclass::with('sections')->where('school_id', \Auth::user()->school->id)->get();
        $classes_id = Myclass::with('sections')->where('school_id', \Auth::user()->school->id)->pluck('id');
        $sections = Section::with('class')
            ->where('active', 1)
            ->whereIn('class_id', $classes_id)
            ->get();
        $form_nums = $this->userService->getFormNumbersArray($sections);
        $houses = House::all();

        session([
            'register_role' => 'student',
            'register_role_action' => 'tct_student',
            'register_sections' => $sections,
            'register_forms' => $classes,
            'register_class' => $classes_id,
            'register_house' => $houses,
            'register_numbers' => $form_nums,
            'tct_id' => $this->userService->getTCTID(),
        ]);
        return view('auth.tct_register');
        // return redirect()->route('tct_register');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTCTRegistrationForm()
    {
        return view('auth.tct_register');
    }

    /**
     * @param $section_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sectionStudents($section_id)
    {
        $students = $this->userService->getSectionStudentsWithSchool($section_id);

        return view('profile.section-students', compact('students'));
    }

    public function sectionTCTStudents($section_id)
    {
        $students = $this->userService->getTCTSectionStudentsWithSchool($section_id);
        $section = Section::find($section_id);
        return view('profile.section-tct-students', compact('students', 'section'));
    }

    public function houseTCTStudents($house_id)
    {
        $students = \App\StudentInfo::where(
            [
                'session' => now()->year,
                'house_id' => $house_id
            ]
        )
            ->orderBy('form_id', 'desc')
            ->orderBy('group', 'asc')
            ->get();
        $house = House::find($house_id);

        return view('profile.house-tct-students', compact('students', 'house'));
    }

    /**
     * @param $section_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function promoteSectionStudents(Request $request, $section_id)
    {
        if ($this->userService->hasSectionId($section_id))
            return $this->userService->promoteSectionStudentsView(
                $this->userService->getSectionStudentsWithStudentInfo($request, $section_id),
                Myclass::with('sections')->bySchool(\Auth::user()->school_id)->get(),
                $section_id
            );
        else
            return $this->userService->promoteSectionStudentsView([], [], $section_id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function promoteSectionStudentsPost(Request $request)
    {
        return $this->userService->promoteSectionStudentsPost($request);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changePasswordGet()
    {
        return view('profile.change-password');
    }

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePasswordPost(ChangePasswordRequest $request)
    {
        if (Hash::check($request->old_password, Auth::user()->password)) {
            $request->user()->fill([
                'password' => Hash::make($request->new_password),
            ])->save();

            return back()->with('status', __('Saved'));
        }

        return back()->with('error-status', __('Passwords do not match.'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function impersonateGet()
    {
        if (app('impersonate')->isImpersonating()) {
            Auth::user()->leaveImpersonation();
            return (Auth::user()->role == 'master') ? redirect('/masters') : redirect('/home');
        } else {
            return view('profile.impersonate', [
                'other_users' => $this->user->where('id', '!=', auth()->id())
                    ->where('role', '!=', 'student')->get(['id', 'name', 'role'])
            ]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function impersonate(ImpersonateUserRequest $request)
    {
        $user = $this->user->find($request->id);
        Auth::user()->impersonate($user);
        return redirect('/home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $password = $request->password;
            $tb = $this->userService->storeStudent($request);
            try {
                // Fire event to store Student information
                if (event(new StudentInfoUpdateRequested($request, $tb->id))) {
                    // Fire event to send welcome email
                    event(new UserRegistered($tb, $password));
                } else {
                    throw new \Exeception('Event returned false');
                }
            } catch (\Exception $ex) {
                Log::info('Email failed to send to this address: ' . $tb->email . '\n' . $ex->getMessage());
            }
        });

        return back()->with('status', __('Saved'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TCTCreateUserRequest $request
     *
     * 
     */
    public function tct_store(TCTCreateUserRequest $request)
    {
        $tb = $this->userService->storeTCTStudent($request);
        event(new TCTStudentInfoUpdateRequested($request, $tb->id));
        return redirect('register/tct_student')->with('status', __('Saved'));
    }

    public function tct_delete_student($id)
    {
        $user = User::findOrFail($id);
        $user->payments()->delete();
        $user->subjectAssigned()->delete();
        $user->feesAssigned()->delete();
        $user->reinstate()->delete();
        $user->inactive()->delete();
        $user->regrecord()->delete();
        $user->studentInfo->firstorFail()->delete();
        $user->delete();
        $school_id = auth()->user()->school_id;
        Cache::forget('studentQuery-' . $school_id);
        Cache::forget('sections-' . $school_id);
        Cache::forget('sectionsActive-' . $school_id);
        Cache::forget('sectionsActive-' . $school_id);
        Cache::forget('houses-' . $school_id);
        return redirect('home')->with('status', __('Deleted succesfully'));
    }

    /**
     * @param CreateAdminRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAdmin(CreateAdminRequest $request)
    {
        $password = $request->password;
        $tb = $this->userService->storeAdmin($request);
        try {
            // Fire event to send welcome email
            // event(new userRegistered($userObject, $plain_password)); // $plain_password(optional)
            event(new UserRegistered($tb, $password));
        } catch (\Exception $ex) {
            Log::info('Email failed to send to this address: ' . $tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateTeacherRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTeacher(CreateTeacherRequest $request)
    {
        $password = $request->password;
        $tb = $this->userService->storeStaff($request, 'teacher');
        try {
            // Fire event to send welcome email
            event(new UserRegistered($tb, $password));
        } catch (\Exception $ex) {
            Log::info('Email failed to send to this address: ' . $tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateAccountantRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAccountant(CreateAccountantRequest $request)
    {
        $password = $request->password;
        $tb = $this->userService->storeStaff($request, 'accountant');
        try {
            // Fire event to send welcome email
            event(new UserRegistered($tb, $password));
        } catch (\Exception $ex) {
            Log::info('Email failed to send to this address: ' . $tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateLibrarianRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLibrarian(CreateLibrarianRequest $request)
    {
        $password = $request->password;
        $tb = $this->userService->storeStaff($request, 'librarian');
        try {
            // Fire event to send welcome email
            event(new UserRegistered($tb, $password));
        } catch (\Exception $ex) {
            Log::info('Email failed to send to this address: ' . $tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return UserResource
     */
    public function show($user_code)
    {
        $user = $this->userService->getUserByUserCode($user_code);
        if (!$user) {
            return view('profile.no-user');
        }
        
        $feeList = [];
        $subjectList = [];
        $sessions = []; // Initialize sessions outside if block

        $firstYear = "20" . substr($user->studentInfo->tct_id, 0, 2);
        $years = range(now()->year, $firstYear);

        if ($assignedCount = $user->fees_assigned_count > 0) {
            // Eager load all fees with relationships in one query
            $all_fees_assigned = \App\Assign::with(['fees.fee_type', 'fees.fee_channel'])
                ->where('user_id', $user->id)
                ->orderBy('session', 'desc')
                ->groupBy('fee_id')
                ->get();
            
            // Get sessions from the loaded assigns (avoid duplicate query)
            $sessions = $all_fees_assigned->pluck('session')->unique()->values()->toArray();
            
            // Extract fees with channels from the already loaded assigns (no duplicate query)
            $feesWithChannels = $all_fees_assigned->pluck('fees')->keyBy('id');
            
            foreach ($sessions as $session) {
                $fees_assigned = $all_fees_assigned->where('session', $session);
                if ($fees_assigned->first()) {
                    $feeList[$session]['year'] = $session;
                    $feeIDs = $fees_assigned->pluck('fee_id')->toArray();
                    $feeTypeIDs = $fees_assigned->pluck('fees.fee_type_id')->toArray();
                    $feeType = $fees_assigned->pluck('fees.fee_type.name')->toArray();
                    $feeList[$session]['types'] = $feeType;
                    $feeList[$session]['fee_id'] = $feeIDs;
                }
            }
        } else {
            $fees_assigned = "";
            $feesWithChannels = collect([]);
        }

        // Build fee channels from already loaded data
        $feeChannels = [];
        foreach ($feeList as $session => $fees) {
            $firstFeeID = $fees['fee_id'][0] ?? null;
            if ($firstFeeID && isset($feesWithChannels[$firstFeeID])) {
                $fee = $feesWithChannels[$firstFeeID];
                $feeChannels[$session] = $fee->fee_channel ? $fee->fee_channel->name : 'No Channel Assigned';
            } else {
                $feeChannels[$session] = 'No Fees Assigned';
            }
        }

        // Get all fee IDs from feeList
        $allFeeIds = collect($feeList)->pluck('fee_id')->flatten()->unique()->filter()->all();

        // Pre-load all payments for all fees and sessions at once
        $allPayments = \App\Payment::whereIn('fee_id', $allFeeIds)
            ->where('user_id', $user->id)
            ->whereIn('session', $sessions)
            ->get()
            ->groupBy(function($payment) {
                return $payment->fee_id . '_' . $payment->session;
            });

        // Pre-load all payments for display table with relationships
        $allPayForDisplay = \App\Payment::with('fees.fee_type')
            ->where('user_id', $user->id)
            ->orderBy('pay_date', 'desc')
            ->get();
            
        // Consolidate PaymentMigrate queries - load once with all needed data
        $oldPaymentData = \App\PaymentMigrate::where('tct_id', $user->studentInfo->tct_id)
            ->whereIn('year', $sessions)
            ->orderBy('pay_date', 'desc')
            ->get();
            
        // Old payments for display
        $oldPayments = $oldPaymentData;
        
        // Pre-load fee types from PaymentMigrate for each year
        $oldPaymentFeeTypes = $oldPaymentData
            ->groupBy('year')
            ->map(function($items) {
                return $items->pluck('fee_type')->unique()->values()->toArray();
            });
            
        // Pre-calculate old payment amounts for each fee type and session (pre-2020)
        $oldPaymentAmounts = [];
        foreach ($oldPaymentData->groupBy('year') as $year => $payments) {
            $oldPaymentAmounts[$year] = $payments->groupBy('fee_type')->map(function($items) {
                return $items->sum('amount');
            })->toArray();
        }

        // Cache school type IDs for 60 minutes - these rarely change
        $schoolTypeIDs = \Cache::remember('school_type_ids', 60, function() {
            return \App\FeeType::whereIn('name', ['Term 1', 'Term 2', 'Term 3', 'Term 4'])
                ->pluck('id')
                ->toArray();
        });
        // Pre-calculate school fees data for pre-2020 sessions
        $schoolFeeData = [];
        foreach ($sessions as $session) {
            if ($session < "2020") {
                $sessionFeeIds = $feeList[$session]['fee_id'] ?? [];
                // Use pre-loaded fees instead of querying again
                $schoolAssign = $feesWithChannels->whereIn('id', $sessionFeeIds)
                    ->whereIn('fee_type_id', $schoolTypeIDs)
                    ->sum('amount');
                $schoolFeeData[$session] = [
                    'typeIDs' => $schoolTypeIDs,
                    'schoolAssign' => $schoolAssign
                ];
            }
        }

        $subID = \App\SubjectAssign::with('subject')->where(['user_id' => $user->id])->get();

        foreach ($years as $session) {
            $subjectList[$session] = $subID->where('session', $session)->pluck('option')->toArray();
        }
        
        if ($user->studentInfo->section != NULL) {
            $optionSubs = \App\SubjectClass::whereHas('subject', function ($q) {
                $q->where('active', 1);
            })->where([
                'class_id' => $user->studentInfo->section->class_id,
                'active' => 1,
            ])->pluck('subject_id')->toArray();
            
            // Pre-load all subjects to avoid N+1 queries in view
            $allSubjects = \App\Subject::whereIn('id', $optionSubs)->get()->keyBy('id');
        } else {
            // Only update if section is actually NULL to avoid unnecessary database writes
            $optionSubs = [];
            $allSubjects = collect([]);
        }
        
        // Build comprehensive enrollment history with gap years
        $enrollmentHistory = [];
        $currentYear = now()->year;
        
        // Calculate available student years (from enrollment to present)
        $firstYear = $currentYear;
        if (!empty($user->studentInfo->tct_id) && is_numeric(substr($user->studentInfo->tct_id, 0, 2))) {
            $firstYear = intval("20" . substr($user->studentInfo->tct_id, 0, 2));
        }
        $availableStudentYears = range($currentYear, $firstYear);
        
        // Fetch all enrollment data
        $records = [];
        
        // Get current year enrollment from StudentInfo
        if (in_array($currentYear, $availableStudentYears)) {
            $currentRecord = (object)[
                'session' => $user->studentInfo->session,
                'form_name' => ($user->studentInfo->section && $user->studentInfo->section->class) 
                    ? $user->studentInfo->section->class->class_number . $user->studentInfo->section->section_number 
                    : 'N/A',
                'form_num' => $user->studentInfo->form_num,
                'house_name' => $user->studentInfo->house ? $user->studentInfo->house->house_name : 'N/A',
                'status' => ucfirst($user->studentInfo->group),
                'category_id' => $user->studentInfo->category_id,
                'channel_name' => $user->studentInfo->channel ? $user->studentInfo->channel->name : 'Not Assigned',
                'created_at' => $user->studentInfo->updated_at,
                'notes' => $user->studentInfo->reg_notes,
            ];
            $records[$user->studentInfo->session] = $currentRecord;
        }
        
        // Get historical enrollment from regrecords
        $regrecords = \App\Regrecord::with(['section.class', 'house', 'channel'])
            ->where('user_id', $user->id)
            ->get();
        
        foreach ($regrecords as $r) {
            if (!isset($records[$r->session])) {
                $records[$r->session] = (object)[
                    'session' => $r->session,
                    'form_name' => ($r->section && $r->section->class) 
                        ? $r->section->class->class_number . $r->section->section_number 
                        : 'N/A',
                    'form_num' => $r->form_num,
                    'house_name' => $r->house ? $r->house->house_name : 'N/A',
                    'status' => ucfirst($r->status),
                    'category_id' => $r->category_id,
                    'channel_name' => $r->channel ? $r->channel->name : 'Not Assigned',
                    'created_at' => $r->created_at,
                    'notes' => $r->notes,
                ];
            }
        }
        
        // Build complete history including gap years
        foreach ($availableStudentYears as $year) {
            if (isset($records[$year])) {
                $enrollmentHistory[] = $records[$year];
            } else {
                // Gap year - no enrollment data
                $enrollmentHistory[] = (object)[
                    'session' => $year,
                    'form_name' => 'Unavailable',
                    'form_num' => 'N/A',
                    'house_name' => 'N/A',
                    'status' => 'Unavailable',
                    'category_id' => 'N/A',
                    'channel_name' => 'N/A',
                    'created_at' => null,
                    'notes' => null,
                ];
            }
        }
        
        return view('profile.user', compact(
            'user', 
            'assignedCount', 
            'feeList', 
            'sessions', 
            'fees_assigned', 
            'optionSubs', 
            'subjectList', 
            'feeChannels',
            'feesWithChannels',
            'allPayments',
            'allPayForDisplay',
            'oldPayments',
            'schoolFeeData',
            'allSubjects',
            'subID',
            'oldPaymentFeeTypes',
            'oldPaymentAmounts',
            'enrollmentHistory'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->user->find($id);
        $classes = Myclass::query()
            ->bySchool(\Auth::user()->school_id)
            ->pluck('id')
            ->toArray();

        $sections = Section::query()
            ->whereIn('class_id', $classes)
            ->get();

        $departments = Department::query()
            ->bySchool(\Auth::user()->school_id)
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'sections' => $sections,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {

        DB::transaction(function () use ($request) {
            $tb = $this->user->find($request->user_id);
            $tb->name = $request->name;
            $tb->email = (!empty($request->email)) ? $request->email : '';
            $tb->nationality = (!empty($request->nationality)) ? $request->nationality : '';
            $tb->phone_number = $request->phone_number;
            $tb->address = (!empty($request->address)) ? $request->address : '';
            $tb->about = (!empty($request->about)) ? $request->about : '';
            if (!empty($request->pic_path)) {
                $tb->pic_path = $request->pic_path;
            }
            if ($request->user_role == 'teacher') {
                $tb->department_id = $request->department_id;
                $tb->section_id = $request->class_teacher_section_id;
            }
            if ($tb->save()) {
                if ($request->user_role == 'student') {
                    try {
                        // Fire event to store Student information
                        event(new StudentInfoUpdateRequested($request, $tb->id));
                    } catch (\Exception $ex) {
                        Log::info('Failed to update Student information, Id: ' . $tb->id . 'err:' . $ex->getMessage());
                    }
                }
            }
        });

        return back()->with('status', __('Saved'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function tct_administration_update(Request $request)
    {
        $tb = User::find($request->user_id)->studentInfo;
        $tb2 = User::find($request->user_id);
        if ($tb->form_id != $request->section) {
            $tb->form_id = $request->section;
            $tb->form_num = $this->userService->getMaxFormNumber($request->section);
            $tb2->section_id = $request->section;
            $tb2->save();
        } else {
            $tb->form_num = $request->form_num;
        }
        $tb->house_id = $request->house;
        $tb->group = $request->status;
        $tb->session = $request->session;
        $tb->reg_notes = $request->notes;
        $tb->save();
        return redirect("/user/$tb2->student_code");
    }

    public function tct_other_update(Request $request)
    {
        // print($request);
        $tb = User::find($request->user_id)->studentInfo;
        $tb2 = User::find($request->user_id);
        $tb2->lst_name = $request->lst_name;
        $tb2->given_name = $request->given_name;
        $tb->birthday = $request->birthday;
        $tb->category_id = $request->category;
        $tb->church = $request->church;
        $tb2->village = $request->village;
        $tb2->nationality = $request->nationality;
        $tb2->blood_group = $request->blood_group;
        $tb->father_name = $request->father_name;
        $tb->father_phone_number = $request->father_phone_number;
        $tb->father_occupation = $request->father_occupation;
        $tb->mother_name = $request->mother_name;
        $tb->mother_phone_number = $request->mother_phone_number;
        $tb->mother_occupation = $request->mother_occupation;
        $tb->save();
        $tb2->save();

        return redirect("/user/$tb2->student_code");
    }

    public function promote_tct_student(Request $request)
    {
        // return $request;

        // Insert into Regrecord
        $request->validate([
            'section' => 'required',
            'house' => 'required',
        ]);
        // return $request;
        $user = User::find($request->user_id);

        $tb = new Regrecord();
        $tb->user_id = $user->id;
        $tb->session = $user->studentInfo->session;
        $tb->form_id = $user->studentInfo->form_id;
        $tb->form_num = $user->studentInfo->form_num;
        $tb->house_id = $user->studentInfo->house_id;
        $tb->status = $user->studentInfo->group;
        $tb->category_id = $user->studentInfo->category_id;
        $tb->fee_id = ($user->studentInfo->channel_id == NULL) ? 0 : $user->studentInfo->channel_id;
        $tb->notes = $user->StudentInfo->reg_notes;
        $tb->save();

        // Update StudentInfo Table
        $tb2 = $user->studentInfo;
        $tb2->form_id = $request->section;
        $tb2->form_num = $this->userService->getMaxFormNumber($request->section);
        $tb2->session = $request->session;
        $tb2->reg_notes = $request->notes;
        // $tb2->form_id = $request->section;
        $tb2->group = $request->status;
        $tb2->assigned = 0;
        $tb2->channel_id = '';
        $tb2->save();
        $school_id = auth()->user()->school_id;
        Cache::forget('studentQuery-' . $school_id);
        Cache::forget('sections-' . $school_id);
        Cache::forget('sectionsActive-' . $school_id);
        Cache::forget('sectionsActive-' . $school_id);
        Cache::forget('houses-' . $school_id);

        return redirect("/user/$user->student_code");
    }

    /**
     * Activate admin
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateAdmin($id)
    {
        $admin = $this->user->find($id);

        if ($admin->active !== 0) {
            $admin->active = 0;
        } else {
            $admin->active = 1;
        }

        $admin->save();

        return back()->with('status', __('Saved'));
    }

    /**
     * Deactivate admin
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateAdmin($id)
    {
        $admin = $this->user->find($id);

        if ($admin->active !== 1) {
            $admin->active = 1;
        } else {
            $admin->active = 0;
        }

        $admin->save();

        return back()->with('status', __('Saved'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return void
     */
    public function destroy($id)
    {
        // return ($this->user->destroy($id))?response()->json([
        //   'status' => 'success'
        // ]):response()->json([
        //   'status' => 'error'
        // ]);
    }

    public function prefectTCTStudents()
    {
        $prefects = \App\StudentInfo::with('student', 'house', 'section.class')->where('session', now()->year)
            ->whereIn('group', ['Prefect', 'Head Prefect'])
            ->orderBy('group', 'asc')
            ->orderBy('house_id', 'asc')
            ->get();
        return view('profile.prefects-tct-students', [
            'prefects' => $prefects,
        ]);
    }

    public function otherDistributions()
    {
        $churches = DB::table('student_infos')
            ->select('church', DB::raw('count(*) as count'))
            ->where('session', now()->year)
            ->groupBy('church')
            ->get();

        $villages = DB::table('student_infos')
            ->join('users', 'student_infos.student_id', '=', 'users.id')
            ->select('users.village', DB::raw('count(*) as count'))
            ->where('student_infos.session', now()->year)
            ->groupBy('users.village')
            ->get();

        $countries = DB::table('student_infos')
            ->join('users', 'student_infos.student_id', '=', 'users.id')
            ->select('users.nationality', DB::raw('count(*) as count'))
            ->where('student_infos.session', now()->year)
            ->groupBy('users.nationality')
            ->get();

        // return $villages;
        return view('profile.student-tct-other', compact('churches', 'villages', 'countries'));
    }
}
