<?php

namespace App\Http\Controllers;

use App\subjectAssign;
use Illuminate\Http\Request;

class SubjectAssignController extends Controller
{

    public $options = [
        '1' => 'option1',
        '2' => 'option2',
        '3' => 'option3',
        '4' => 'option4',
        '5' => 'option5'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'session' => 'required',
        ]);

        $options = $this->options;
        foreach ($options as $optionNum => $optionID) {
            if ($request->{$optionID} != NULL) {
                $tb = new SubjectAssign;
                $tb->user_id = $request->user_id;
                $tb->subject_id = $request->{$optionID};
                $tb->option = $optionNum;
                $tb->session = now()->year;
                $tb->save();
            }
        }

        return back()->with('status', __('Updated'));
    }

    public function reassign(Request $request)
    {
        // return $request;

        $request->validate([
            'session' => 'required',
        ]);
        $options = $this->options;

        foreach ($options as $optionNum => $optionID) {
            $existing = \App\SubjectAssign::where([
                'user_id' => $request->user_id,
                // 'subject_id' => $optionID,
                'option' => $optionNum,
                'session' => $request->session,
            ])->get();

            if ($existing->first()) {
                if ($request->{$optionID} == NULL) {
                    $tb = \App\SubjectAssign::find($existing->first()->id);
                    $tb->delete();
                } else {
                    //
                    $tb = $existing->first();
                    $tb->subject_id = $request->{$optionID};
                    $tb->save();
                }
            } elseif ($request->{$optionID} != NULL) {
                $tb = new SubjectAssign;
                $tb->user_id = $request->user_id;
                $tb->subject_id = $request->{$optionID};
                $tb->option = $optionNum;
                $tb->session = now()->year;
                $tb->save();
            }
        }

        return back()->with('status', __('Updated'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\subjectAssign  $subjectAssign
     * @return \Illuminate\Http\Response
     */
    public function show(subjectAssign $subjectAssign)
    {
        //
    }

    public function showForm(Request $request)
    {
        // return $request;

        $user = \App\User::find($request->user_id);
        $session = $request->session;
        return view('subject.subjectAssignForm', compact('user', 'session'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\subjectAssign  $subjectAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(subjectAssign $subjectAssign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\subjectAssign  $subjectAssign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subjectAssign $subjectAssign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\subjectAssign  $subjectAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy(subjectAssign $subjectAssign)
    {
        //
    }
}
