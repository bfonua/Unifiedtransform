<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fee;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fees = \App\Fee::bySchool(\Auth::user()->school_id)->get();
        return view('fees.all', ['fees' => $fees]);
    }

    public function tct_index()
    {
        $fees = \App\Fee::where('active', 1)->simplepaginate(65);
        return view('fees.tct_all', ['fees' => $fees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fees.create');
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
            'fee_name' => 'required|string|max:255',
        ]);
        $fee = new \App\Fee;
        $fee->fee_name = $request->fee_name;
        $fee->school_id = \Auth::user()->school_id;
        $fee->user_id = \Auth::user()->id;
        $fee->save();
        return back()->with('status', __('Saved'));
    }

    public function tct_store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'session' => 'required',
        ]);
        $fee =  \App\Fee::firstOrNew(
            [
                'school_id' => \Auth::user()->school_id,
                'fee_channel_id' => $request->channel,
                'fee_type_id' => $request->type,
                'session' => $request->session,
            ]
        );
        // $fee->fee_name = $request->name;
        // $fee->school_id = \Auth::user()->school_id;
        $fee->user_id = \Auth::user()->id;
        // $fee->fee_channel_id = $request->channel;
        // $fee->fee_type_id = $request->type;
        $fee->amount = $request->amount;
        // $fee->amount = $request->session;
        $fee->active = $request->active;
        $fee->save();
        return back()->with('status', __('Saved'));
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
        $update = [
            'school_id' => \Auth::user()->school_id,
            'fee_channel_id' => $request->channel,
            'fee_type_id' => $request->type,
            'session' => $request->session,
            'amount' => $request->amount,
            'active' => $request->active,
        ];
        $fee = \App\Fee::find($id);
        $fee->update($update);
        $fee->fee_name = $request->name;
        $fee->user_id = \Auth::user()->id;
        $fee->save();
        return back()->with('status', __('Updated'));
    }

    /**
     * Update fees for the new session
     * Archives the current fees for the previous session, creates new fees for the new session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSession(Request $request)
    {
        // return $request;
        // $latestSession = \App\Fee::max('session');
        // return \App\Fee::where('session', '2020')->update([
        //     'active' => 1,
        // ]);

        // Get current active Fees
        $currentFees = \App\Fee::where('active', 1)->get();

        // Make current active Fees inactive
        \App\Fee::where('active', 1)->update([
            'active' => 0,
        ]);
        // Insert new fee records for current session
        foreach ($currentFees as $fee) {
            $newChannel = \App\FeeChannel::find($fee->fee_channel_id)->name;
            $newFeeChannel = \App\FeeChannel::where([
                'name' => $newChannel,
                'session' => $request->session,
            ])->first();
            $newFee = \App\Fee::create([
                'school_id' => \Auth::user()->school_id,
                'user_id' => \Auth::user()->id,
                'fee_channel_id' => $newFeeChannel->id,
                'fee_type_id' => $fee->fee_type_id,
                'amount' => $fee->amount,
                'session' => $request->session,
                'active' => 1,
            ]);
            $newFee->save();
            // return $newFee;
        }

        return back()->with('status', __('Updated'));
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
