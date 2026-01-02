<?php

namespace App\Http\Controllers;

use App\School;
use Illuminate\Http\Request;

class MasterController extends Controller
{

    public function index() {
        return view('masters.index');
    }

    public function enterSchool($school_id) {
        // Verify the school exists
        $school = School::findOrFail($school_id);
        
        // Store the school_id in session
        session(['master_school_id' => $school_id]);
        
        // Redirect to home dashboard
        return redirect('/home')->with('status', __('Entered') . ' ' . $school->name);
    }

    public function leaveSchool() {
        // Clear the school context from session
        session()->forget('master_school_id');
        
        // Redirect back to masters page
        return redirect('/masters')->with('status', __('Left school portal'));
    }
}
