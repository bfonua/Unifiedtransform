<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the TCT test page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // You can add your bespoke query here
        // Example query - replace with your actual query
        $testData = DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->where('active', 1)
            ->orderBy('created_at', 'desc')
            ->limit(50) // Increased from 10 to 50, or remove this line entirely for all records
            ->get();

        return view('tct-test', compact('testData'));
    }

    /**
     * Handle any custom queries or operations
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function customQuery(Request $request)
    {
        // Add your custom query logic here
        
        return view('tct-test');
    }
}
