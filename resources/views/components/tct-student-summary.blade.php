<div id = "student_summary" class="row">
    <h3><a href="{{url('/user/'.$user->student_code)}}">{{$user->given_name.' '.$user->lst_name}} </a><small>TCT ID: {{$user->studentInfo->tct_id}}</small></h3>
    <table class="table">
        <tr>
            <td colspan="2">
                <strong>
                    {{($user->studentInfo->session == date('Y'))?'Registered':'Archived'}} 
                    {{-- / {{($user->active)? 'Active' : 'Inactive'}} --}}
                    
                    @if ($user->active)
                        {{($user->studentInfo->session == date('Y'))?'/ Active':'/ Graduated'}} 
                    @else
                        @php
                            $inactive_request = $user->inactive->sortByDesc('created_at')->first();
                            $inactive_type = $inactive_request->type;
                            $inactive_id = $inactive_request->id;
                            $reinstate = false;
                        @endphp
                        / Inactive / {{ucfirst($inactive_type)}} 
                        @if(count($user->reinstate->where('inactive_id',$inactive_id)) > 0)
                            @php
                                $reinstate = true;
                                $reinstate_request = $user->reinstate->where('inactive_id',$inactive_id)->first();
                                $approved = $reinstate_request->approved;
                            @endphp
                            / Reinstated
                            @if($approved)
                                / Approved
                            @else
                                / <b class="text-danger"> Not Approved</b>
                            @endif
                                {{-- ({{($approved)?' Approved': <div class="text-danger">Not Approved</div>}})</ --}}
                        @endif    
                    @endif
                    / {{ucfirst($user->studentInfo->group)}} / Category: {{$user->studentInfo->category_id}} 
                </strong>
            </td>
            <td>
                <strong>Session: </strong> {{$user->studentInfo->session}}
            </td>
        </tr>
        <tr>    
            <td>
                <strong>Form:</strong> {{$user->studentInfo->section->class->class_number}}{{$user->studentInfo->section->section_number}}(#{{$user->studentInfo->form_num}})
            </td>
            <td>
                <strong>House:</strong> {{$user->studentInfo->house->house_name}}
            </td>
            <td>
                @if($user->studentInfo->assigned)
                <strong>Last Fee Channel:</strong> {{\App\FeeChannel::find($user->studentInfo->channel_id)->name}}
                @else
                    <strong>Not assigned</strong>
                @endif
            </td>
        </tr>

    </table>
</div>
