<?php

namespace App\Http\Controllers;

use App\SubjectClass;
use Illuminate\Http\Request;
use App\Services\User\UserService;
use App\User;


class SubjectClassController extends Controller
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
        $sections = \App\Section::with('class')->withCount(['subjects', 'students' => function ($q) {
            $q->where('active', 1);
        }])->where('active', 1)
        ->orderBy('class_id')
        ->orderBy('section_number', 'asc')
        ->get();
        return view('subject.assign_subject_class', compact('sections'));
    }

    public function sectionfeeList(Request $request)
    {
        // return $request->id;
        $students = $this->userService->getTCTSectionStudentsWithSubject($request->id);
        $section = \App\Section::with('class')->find($request->id);
        $subjectClass = \App\SubjectClass::with('subject')->whereHas("subject", function($q) {
            $q->where('active', 1);
        })->where('class_id', $section->class->id)->where('active', 1)->get();
        return view('subject.section-tct-subject', compact('students', 'section', 'subjectClass'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\subjectClass  $subjectClass
     * @return \Illuminate\Http\Response
     */
    public function show(subjectClass $subjectClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\subjectClass  $subjectClass
     * @return \Illuminate\Http\Response
     */
    public function edit(subjectClass $subjectClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\subjectClass  $subjectClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subjectClass $subjectClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\subjectClass  $subjectClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(subjectClass $subjectClass)
    {
        //
    }
}
