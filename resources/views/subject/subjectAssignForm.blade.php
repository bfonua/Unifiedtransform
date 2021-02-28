@extends('layouts.app')

@if(count(array($user)) > 0)
  @section('title', $user->student_code.' - '.$user->given_name)
@endif

@php $userSer = $user; @endphp
@inject('userSer', 'App\Services\User\UserService')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="panel panel-default">
                <div class="page-panel-title">
                    <small> @component('components.tct-student-summary',['user'=>$user])
                        @endcomponent 
                    </small>
                </div>
                <br>
                <div class="container col-md-5">
                    <h4>Subjects Assigned</h4>
                    <br>No subjects assigned for {{now()->year}}<br>
                </div>
                <div class="container col-md-5">
                    <h4>Reassign Options</h4>
                    <div class="panel-body">
                        @lang('No Related Data Found.')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
