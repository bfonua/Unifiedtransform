<?php

namespace App\Http\Controllers;

use App\Section as Section;
use App\Http\Resources\SectionResource;
use App\Regrecord as Regrecord;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $school = \Auth::user()->getSchool();
        $classes = \App\Myclass::bySchool(\Auth::user()->getSchoolId())->withCount(['sections' => function ($q) {
            $q->where('active', 1);
        }])
            ->get();
        $sections = \App\Section::with('class')->withCount('students')
            ->where('active', 1)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();
        return view('school.sections', [
            'classes' => $classes,
            'sections' => $sections,
            'school' => $school,

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
        $request->validate([
            'section_number' => 'required',
            'room_number' => 'required|numeric',
            'class_id' => 'required|numeric',
        ]);
        $tb = new Section;
        $tb->section_number = $request->section_number;
        $tb->room_number = $request->room_number;
        $tb->class_id = $request->class_id;
        $tb->save();
        return back()->with('status', __('Created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new SectionResource(Section::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tb = Section::find($id);
        $tb->section_number = $request->section_number;
        $tb->room_number = $request->room_number;
        $tb->class_id = $request->class_id;
        return ($tb->save()) ? response()->json([
            'status' => 'success'
        ]) : response()->json([
            'status' => 'error'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tct_update(Request $request, $id)
    {
        $tb = Section::find($id);
        $tb->section_number = $request->section_number;
        $tb->room_number = $request->room_number;
        $tb->class_id = $request->class_number;
        $tb->active = $request->section_active;
        $tb->save();
        return redirect('school/sections?course=1');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return (Section::destroy($id)) ? response()->json([
            'status' => 'success'
        ]) : response()->json([
            'status' => 'error'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function inactive()
    {

        $school = \Auth::user()->getSchool();
        $classes = \App\Myclass::bySchool(\Auth::user()->getSchoolId())
            ->get();
        $classeIds = \App\Myclass::bySchool(\Auth::user()->getSchoolId())
            ->pluck('id')
            ->toArray();
        $sections = \App\Section::whereIn('class_id', $classeIds)
            ->where('active', 0)
            ->orderBy('class_id')
            ->orderBy('section_number', 'asc')
            ->get();

        return view('school.inactiveSections', [
            'classes' => $classes,
            'sections' => $sections,
            'school' => $school,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sectionByYear()
    {
        $years = Regrecord::groupBy('session')->orderBy('session', 'desc')->pluck('session')->toArray();
        $list = [];
        foreach ($years as $year) {
            $sections = Regrecord::where('session', $year)->groupBy('form_id')->orderBy('form_id', 'desc')->pluck('form_id')->toArray();
            $list[$year] = [
                'sections' => $sections,
            ];
        }
        return view('school.sections-year', [
            'list' => $list,
        ]);
    }
}
