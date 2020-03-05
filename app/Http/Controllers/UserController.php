<?php

namespace App\Http\Controllers;

use App\User;
use App\House;
use App\Myclass;
use App\Section;
use App\Regrecord;
use App\Department;
use App\StudentInfo;
use Illuminate\Http\Request;
use App\Events\UserRegistered;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\StudentInfoUpdateRequested;
use App\Events\TCTStudentInfoUpdateRequested;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\CreateAdminRequest;
use App\Http\Requests\User\CreateTeacherRequest;
use App\Http\Requests\User\TCTCreateUserRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\CreateLibrarianRequest;
use App\Http\Requests\User\ImpersonateUserRequest;
use App\Http\Requests\User\CreateAccountantRequest;

/**
 * Class UserController.
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index($school_code, $student_code, $teacher_code)
    {
        session()->forget('section-attendance');

        if ($this->userService->isListOfStudents($school_code, $student_code)) {
            return $this->userService->indexView('list.student-list', $this->userService->getStudents());
        } elseif ($this->userService->isListOfTeachers($school_code, $teacher_code)) {
            return $this->userService->indexView('list.teacher-list', $this->userService->getTeachers());
        } else {
            return view('home');
        }
    }

    public function tct_index($school_code, $student_code, $teacher_code)
    {
        // session()->forget('section-attendance');

        if ($this->userService->isListOfStudents($school_code, $student_code)) {
            return $this->userService->indexTCTView('list.tct-student-list', $this->userService->getTCTStudents(), 'registered');
        } else {
            return view('home');
        }
    }

    public function tct_list_archive()
    {
        return $this->userService->indexTCTView('list.tct-student-list', $this->userService->getTCTArchive(), 'archived');
    }

    /**
     * @param $school_code
     * @param $role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexOther($school_code, $role)
    {
        if ($this->userService->isAccountant($role)) {
            return $this->userService->indexOtherView('accounts.accountant-list', $this->userService->getAccountants());
        } elseif ($this->userService->isLibrarian($role)) {
            return $this->userService->indexOtherView('library.librarian-list', $this->userService->getLibrarians());
        } else {
            return view('home');
        }
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

    public function showTCTRegistrationForm()
    {
        return view('auth.tct_register');
    }

    /**
     * @param $section_id
     *
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
                'house_id' => $house_id,
            ])
        ->orderBy('form_id', 'desc')
        ->orderBy('group', 'asc')
        ->get();
        $house = House::find($house_id);

        return view('profile.house-tct-students', compact('students', 'house'));
    }

    /**
     * @param $section_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function promoteSectionStudents(Request $request, $section_id)
    {
        if ($this->userService->hasSectionId($section_id)) {
            return $this->userService->promoteSectionStudentsView(
                $this->userService->getSectionStudentsWithStudentInfo($request, $section_id),
                Myclass::with('sections')->bySchool(\Auth::user()->school_id)->get(),
                $section_id
            );
        } else {
            return $this->userService->promoteSectionStudentsView([], [], $section_id);
        }
    }

    /**
     * @param Request $request
     *
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
     *
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

            return ('master' == Auth::user()->role) ? redirect('/masters') : redirect('/home');
        } else {
            return view('profile.impersonate', [
                'other_users' => $this->user->where('id', '!=', auth()->id())
                    ->where('role', '!=', 'student')->get(['id', 'name', 'role']),
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
                Log::info('Email failed to send to this address: '.$tb->email.'\n'.$ex->getMessage());
            }
        });

        return back()->with('status', __('Saved'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TCTCreateUserRequest $request
     */
    public function tct_store(TCTCreateUserRequest $request)
    {
        // print($request);
        $tb = $this->userService->storeTCTStudent($request);
        event(new TCTStudentInfoUpdateRequested($request, $tb->id));

        return redirect('register/tct_student')->with('status', __('Saved'));
    }

    /**
     * @param CreateAdminRequest $request
     *
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
            Log::info('Email failed to send to this address: '.$tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateTeacherRequest $request
     *
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
            Log::info('Email failed to send to this address: '.$tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateAccountantRequest $request
     *
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
            Log::info('Email failed to send to this address: '.$tb->email);
        }

        return back()->with('status', __('Saved'));
    }

    /**
     * @param CreateLibrarianRequest $request
     *
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
            Log::info('Email failed to send to this address: '.$tb->email);
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
        $assignedCount = $user->feesAssigned()->count('id');
        $sessions = \App\Assign::where('user_id', $user->id)->orderBy('session', 'desc')->groupBy('session')->pluck('session')->toArray();
        $feeList = [];
        if ($assignedCount > 0) {
            foreach ($sessions as $session) {
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
            }
        } else {
            $fees_assigned = '';
        }
        // return $user;
        return view('profile.user', compact('user', 'assignedCount', 'feeList', 'sessions', 'fees_assigned'));
    }

    public function migrationTest()
    {
        return view('home');
        $toMigrate = DB::table('assignMigrate')->get()->slice(3000);
        $types = ['term1' => 1, 'term2' => 2, 'term3' => 3, 'term4' => 4, 'late' => 5, 'pta' => 6,
            'magazine' => 7,
            'bazaar' => 9,
        ];
        $main = [];
        $count = 1;
        try {
            foreach ($toMigrate as $assignOld) {
                foreach ($types as $type => $type_id) {
                    if ($assignOld->{$type} > 0) {
                        $assign = new \App\Assign();
                        $assign->user_id = \App\User::where('student_code', $assignOld->tct_id)->first()->id;
                        if (9 == $type_id && $assignOld->{$type} < 100) {
                            $tbType = 8;
                        } else {
                            $tbType = $type_id;
                        }
                        if ($assignOld->fee_id < 50) {
                            $tbChannel = $assignOld->fee_id;
                        } elseif ($assignOld->fee_id < 63) {
                            $tbChannel = $assignOld->fee_id - 1;
                        } else {
                            $tbChannel = $assignOld->fee_id - 2;
                        }
                        $assign->fee_id = \App\Fee::where([
                            'session' => $assignOld->session_id + 2015,
                            'fee_type_id' => $tbType,
                            'fee_channel_id' => $tbChannel,
                        ])->first()->id;
                        $assign->session = $assignOld->session_id + 2015;
                        ++$count;
                        echo 'COUNT'.$count;
                    }
                }
            }
        } catch (\Exception $e) {
        }
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
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $tb = $this->user->find($request->user_id);
            $tb->name = $request->name;
            $tb->email = (! empty($request->email)) ? $request->email : '';
            $tb->nationality = (! empty($request->nationality)) ? $request->nationality : '';
            $tb->phone_number = $request->phone_number;
            $tb->address = (! empty($request->address)) ? $request->address : '';
            $tb->about = (! empty($request->about)) ? $request->about : '';
            if (! empty($request->pic_path)) {
                $tb->pic_path = $request->pic_path;
            }
            if ('teacher' == $request->user_role) {
                $tb->department_id = $request->department_id;
                $tb->section_id = $request->class_teacher_section_id;
            }
            if ($tb->save()) {
                if ('student' == $request->user_role) {
                    try {
                        // Fire event to store Student information
                        event(new StudentInfoUpdateRequested($request, $tb->id));
                    } catch (\Exception $ex) {
                        Log::info('Failed to update Student information, Id: '.$tb->id.'err:'.$ex->getMessage());
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
     *
     * @return \Illuminate\Http\Response
     */
    public function tct_administration_update(Request $request)
    {
        // print($request);
        $tb = User::find($request->user_id)->studentInfo;
        $tb2 = User::find($request->user_id);

        if ($tb->form_id != $request->section) {
            $tb2 = User::find($request->user_id);
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
        // print($request);

        // Insert into Regrecord
        $request->validate([
            'section' => 'required',
            'house' => 'required',
        ]);
        // return $request;
        $user = User::find($request->user_id);
        $tb = Regrecord::firstOrCreate(['user_id' => $request->user_id]);
        $tb->user_id = $user->id;
        $tb->session = $user->studentInfo->session;
        $tb->form_id = $user->studentInfo->form_id;
        $tb->form_num = $user->studentInfo->form_num;
        $tb->house_id = $user->studentInfo->house_id;
        $tb->status = $user->studentInfo->group;
        // $tb->reg_date = $user->studentInfo->updated
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

        return redirect("/user/$user->student_code");
    }

    /**
     * Activate admin.
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activateAdmin($id)
    {
        $admin = $this->user->find($id);

        if (0 !== $admin->active) {
            $admin->active = 0;
        } else {
            $admin->active = 1;
        }

        $admin->save();

        return back()->with('status', __('Saved'));
    }

    /**
     * Deactivate admin.
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateAdmin($id)
    {
        $admin = $this->user->find($id);

        if (1 !== $admin->active) {
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
        $prefects = \App\StudentInfo::where('session', now()->year)
            ->whereIn('group', ['Prefect', 'Head Prefect'])
            ->orderBy('group', 'asc')
            ->orderBy('house_id', 'asc')
            ->get();

        return view('profile.prefects-tct-students', [
            'prefects' => $prefects,
        ]);
    }

    public function queryTest()
    {
        $section = \App\Section::where('active', 1)
            ->orderBy('class_id', 'asc')
            ->orderBy('section_number', 'asc')
            ->first();
        // echo('<table>
        //     <thead>
        //         <th>Form</th>
        //         <th>Count</th>
        //         <th>Assign</th>
        //         <th>Payment</th>
        //         <th>Remainging</th>
        //     </thead>
        //     <tbody>');
        // foreach($section as $section){
        //     $assign = $section->totalAssigned()->sum('fees.amount');
        //     $payment = $section->payment()->sum('amount');
        //     $remain = $assign - $payment;
        //     echo('<tr>
        //         <td>'.$section->class->class_number.$section->section_number.'</td>
        //         <td>'.$section->students()->count().'</td>
        //         <td>'.$assign.'</td>
        //         <td>'.$payment.'</td>
        //         <td>'.$remain.'</td>
        //     </tr>');
        // }
        // echo('</tbody>
        // </table>');
    }
}
