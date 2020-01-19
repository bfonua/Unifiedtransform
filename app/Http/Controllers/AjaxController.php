<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getFeeAssignList(Request $request)
    {
        if(isset($request->session)){
            $session = $request->session;
        } else{
            $session = now()->year;
        }
        $types = \App\Fee::where('session', $session)
            // ->where('active', 1)
            ->where('fee_channel_id', $request->channel_id)
            ->get();
            
        return view('tableTemplates.feeAssignListTemplate',[
            'fee_channel_id' => $request->channel_id,
            'types' => $types,
            'session' => $types->first()->session,
        ]);
    }
}
