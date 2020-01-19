@extends('layouts.app')

@if(count(array($user)) > 0)
  @section('title', $user->student_code.' - '.$user->given_name)
@endif

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="panel panel-default">
              @if(count(array($user)) > 0)
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @component('components.tct-user-profile-v3',[
                        'user'=>$user,
                        'assigned'=> ($assignedCount > 0)?1:0,
                        'feeList' => $feeList,
                        'sessions' => $sessions,
                        'fees_assigned' => $fees_assigned,
                    ])
                    @endcomponent
                </div>
              @else
                <div class="panel-body">
                    @lang('No Related Data Found.')
                </div>
              @endif
            </div>
        </div>
    </div>
</div>
@endsection
