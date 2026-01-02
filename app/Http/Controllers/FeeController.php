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
        $fees = \App\Fee::where('active', 1)->with('fee_type', 'fee_channel')->get();
        $feeTypes = \App\FeeType::where('active', 1)->get();
        
        // Get fee channels and sort by form level (F7 first), then IF before NF, then by category
        $feeChannels = \App\FeeChannel::where('active', 1)
            ->with('fees')
            ->where('session', now()->year)
            ->get()
            ->sort(function($a, $b) {
                $nameA = $a->name;
                $nameB = $b->name;
                
                // Extract components: type (IF/NF), form code, category
                $typeA = substr($nameA, 0, 2); // IF or NF
                $formA = substr($nameA, 2, 2); // 07, 06, TV, etc.
                $categoryA = substr($nameA, 4); // C01, C02, C03, etc.
                
                $typeB = substr($nameB, 0, 2);
                $formB = substr($nameB, 2, 2);
                $categoryB = substr($nameB, 4);
                
                // TV (TVET) should come last
                if ($formA == 'TV' && $formB != 'TV') return 1;
                if ($formB == 'TV' && $formA != 'TV') return -1;
                if ($formA == 'TV' && $formB == 'TV') {
                    // Both TV, sort by type (IF before NF), then category
                    if ($typeA != $typeB) return $typeA == 'IF' ? -1 : 1;
                    return strcmp($categoryA, $categoryB);
                }
                
                // Sort forms in descending order (F7 before F6)
                if ($formA != $formB) {
                    return (int)$formB - (int)$formA;
                }
                
                // Same form, sort by type (IF before NF)
                if ($typeA != $typeB) {
                    return $typeA == 'IF' ? -1 : 1;
                }
                
                // Same form and type, sort by category (C01, C02, C03)
                return strcmp($categoryA, $categoryB);
            })
            ->values();
        
        return view('fees.tct_all', compact('fees', 'feeTypes', 'feeChannels'));
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
        // $request->validate([
        //     'fee_name' => 'required|string|max:255',
        // ]);
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
            // 'name' => 'required',
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
        $fee->user_id = \Auth::user()->id;
        $fee->amount = $request->amount;
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
        // Check if fee channels exist for the target session
        $targetSessionChannels = \App\FeeChannel::where('session', $request->session)->count();
        
        if ($targetSessionChannels == 0) {
            return back()->with('error', __('No fee channels found for session') . ' ' . $request->session . '. ' . __('Please update fee channels first in the Fee Channel page before updating fees.'));
        }
        
        // Get current active Fees
        $currentFees = \App\Fee::where('active', 1)->get();
        
        if ($currentFees->count() == 0) {
            return back()->with('error', __('No active fees found to update.'));
        }

        // Make current active Fees inactive
        \App\Fee::where('active', 1)->update([
            'active' => 0,
        ]);
        
        // Insert new fee records for current session
        $created = 0;
        $skipped = 0;
        
        foreach ($currentFees as $fee) {
            $oldChannel = \App\FeeChannel::find($fee->fee_channel_id);
            if (!$oldChannel) {
                $skipped++;
                continue;
            }
            
            $newChannel = $oldChannel->name;
            $newFeeChannel = \App\FeeChannel::where([
                'name' => $newChannel,
                'session' => $request->session,
            ])->first();
            
            // Skip if the fee channel doesn't exist for the target session
            if (!$newFeeChannel) {
                $skipped++;
                continue;
            }
            
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
            $created++;
        }

        $message = __('Updated') . ": Created $created fees";
        if ($skipped > 0) {
            $message .= ", skipped $skipped (missing fee channels)";
        }
        
        return back()->with('status', $message);
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
