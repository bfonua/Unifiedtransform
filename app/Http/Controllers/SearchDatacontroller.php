<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchDatacontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('search');
    }

    public function autocomplete(Request $request)
    {
        // Get school_id - for master users, use session, otherwise use auth user's school
        $school_id = \Auth::user()->role == 'master' && session()->has('master_school_id')
            ? session('master_school_id')
            : \Auth::user()->school_id;

        $data = \App\User::select('name')
            ->where('school_id', $school_id)
            ->where(function($query) use ($request) {
                $query->where("name", "LIKE", "%{$request->input('query')}%")
                    ->orWhere("lst_name", "LIKE", "%{$request->input('query')}%")
                    ->orWhere("given_name", "LIKE", "%{$request->input('query')}%");
            })
            ->get();
        return response()->json($data);
    }

    public function find(Request $request)
    {
        // Get school_id - for master users, use session, otherwise use auth user's school
        $school_id = \Auth::user()->role == 'master' && session()->has('master_school_id')
            ? session('master_school_id')
            : \Auth::user()->school_id;

        $q = $request->get('q');
        return \App\User::where('school_id', $school_id)
            ->where(function($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('lst_name', 'LIKE', "%{$q}%")
                    ->orWhere('given_name', 'LIKE', "%{$q}%")
                    ->orWhere('student_code', 'LIKE', "%{$q}%")
                    // Add support for "lastname firstname"
                    ->orWhereRaw("CONCAT(lst_name, ' ', given_name) LIKE ?", ["%{$q}%"])
                    // Add support for "firstname lastname"
                    ->orWhereRaw("CONCAT(given_name, ' ', lst_name) LIKE ?", ["%{$q}%"]);
            })
            ->where('role', 'student')
            ->orderBy('student_code', 'desc')
            ->get();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
