<?php

namespace App\Http\Controllers;

use App\Subject;
use App\SubjectAssign;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = \App\Myclass::where('options', 1)->get();
        $activeSubjects = \App\Subject::where('active', 1)->orderBy('name', 'asc')->get();
        $inactiveSubjects = \App\Subject::where('active', 0)->orderBy('name', 'asc')->get();
        return view('subject.subject', [
            'classes' => $classes,
            'activeSubjects' => $activeSubjects,
            'inactiveSubjects' => $inactiveSubjects,
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
        // return $request;

        $request->validate([
            'name' => 'required',
            'options' => 'required'
        ]);

        $tb = new Subject;
        $tb->name = $request->name;
        $tb->active = 1;
        $tb->session = now()->year;
        $tb->save();
        $subject_id = $tb->id;

        if (isset($request->options)) {
            foreach ($request->options as $class_id) {
                $tb2 = new \App\SubjectClass;
                $tb2->subject_id = $subject_id;
                $tb2->class_id = $class_id;
                $tb2->session = now()->year;
                $tb2->active = 1;
                $tb2->save();
            }
        }

        return back()->with('status', __('Created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(subject $subject)
    {
        //
    }


    public function show_sections()
    {
        $school = \Auth::user()->school;
        $classes = \App\Myclass::bySchool(\Auth::user()->school->id)
            ->where('options', 1)
            ->get();

        $classSubList = [];
        foreach ($classes as $class) {
            $classSubList[$class->id]['subRecord'] = \App\SubjectClass::whereHas('subject', function ($q) {
                $q->where('active', 1);
            })->where('active', 1)
                ->where('class_id', $class->id)->get();

            $classSubCountList = [];

            foreach ($classSubList[$class->id]['subRecord'] as $sub) {
                $subRecord = $sub->subject;
                $num = $class->students()->where('subject_id', $subRecord->id)->count();
                $classSubCountList[$subRecord->id] = $num;
            }
            $classSubList[$class->id]['subCount'] = $classSubCountList;
        }

        // return $classSubList;
        return view('subject.section_subject', compact('classes', 'classSubList'));
    }

    public function show_section_list($subClass_id)
    {
        // return $subClass_id;

        $subClass = \App\SubjectClass::find($subClass_id);
        $sub = $subClass->subject;
        $class = $subClass->class;

        $students = $class->students()->where('subject_id', $sub->id)->get();
        // return $students;
        return view('subject.subject-tct-students', compact('class', 'sub', 'students'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(subject $subject)
    {
        //
    }

    public function update_subject(Request $request, $id)
    {

        // return $request;


        $request->validate([
            'name' => 'required',
            'options' => 'required'
        ]);

        // Check if Subject should be inactive
        if ($request->sub_active == 0) {
            $tb = Subject::find($id);
            $tb->active = 0;
            $tb->save();
        } else {
            // Make any changes to Subject name
            $tb = Subject::find($id);
            $tb->name = $request->name;
            $tb->save();
            $subject_id = $tb->id;
            // Get list of optionClasses previously created (if any)
            $optionClasses = \App\SubjectClass::where([
                'subject_id' => $id,
                'active' => 1,
            ])->get();
            $optionClassesID = \App\SubjectClass::where([
                'subject_id' => $id,
                'active' => 1,
            ])->pluck('class_id')->toArray();

            $selectedClasses = $request->options;

            // Loop through existing optionClasses, if class not in selected classes then make DB optionClasss record inactive
            foreach ($optionClasses as $dbClass) {
                if (!in_array($dbClass->class_id, $selectedClasses)) {
                    $tb3 = \App\SubjectClass::find($dbClass->id);
                    $tb3->active = 0;
                    $tb3->save();
                }
            }

            // Loop through newly selected optionClasses, if class not in DB then create optionClass otherwise make active
            foreach ($selectedClasses as $selClass) {
                if (!in_array($selClass, $optionClassesID)) {
                    $q = \App\SubjectClass::where([
                        'subject_id' => $id,
                        'active' => 0,
                        'class_id' => $selClass,
                    ])->get();
                    if ($q->first()) {
                        $tb4 = $q->first();
                        $tb4->active = 1;
                        $tb4->save();
                    } else {
                        $tb2 = new \App\SubjectClass;
                        $tb2->subject_id = $id;
                        $tb2->class_id = $selClass;
                        $tb2->session = now()->year;
                        $tb2->active = 1;
                        $tb2->save();
                    }
                }
            }
        }

        return back()->with('status', __('Updated'));
    }

    public function update_inactive_subject(Request $request, $id)
    {
        // return $request;

        $request->validate([
            'name' => 'required',
        ]);

        $tb = Subject::find($id);
        $tb->name = $request->name;
        $tb->active = $request->sub_active;
        $tb->save();

        return back()->with('status', __('Updated'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(subject $subject)
    {
        //
    }
}
