@extends('layouts.app')

@section('title', __('Non-Student Users'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}"><i class="material-icons">home</i> @lang('Home')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('register/teacher') }}" style="background-color: #5cb85c; color: white;">
                        <i class="material-icons">person_add</i> @lang('New Teacher')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('register/accountant') }}" style="background-color: #5bc0de; color: white;">
                        <i class="material-icons">person_add</i> @lang('New Accountant')
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-10" id="main-container">
            <h2>@lang('Non-Student Users')</h2>
            <div class="panel panel-default">
                @if(count($users) > 0)
                <div class="panel-body">
                    <table class="table">
                        <tr>
                            <th>@lang('Action')</th>
                            <th>@lang('Action')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Role')</th>
                            <th>@lang('Code')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Phone Number')</th>
                            <th>@lang('Date Registered')</th>
                        </tr>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                @if($user->active == 0)
                                <a href="{{url('school/activate-user/'.$user->id)}}" class="btn btn-xs btn-success"
                                    role="button"><i class="material-icons">
                                        done
                                    </i>@lang('Activate')</a>
                                @else
                                <a href="{{url('school/deactivate-user/'.$user->id)}}" class="btn btn-xs btn-danger"
                                    role="button"><i class="material-icons">
                                        clear
                                    </i>@lang('Deactivate')</a>
                                @endif
                            </td>
                            <td>
                                <a href="{{url('edit/user/'.$user->id)}}" class="btn btn-xs btn-info"
                                    role="button"><i class="material-icons">
                                        edit
                                    </i> @lang('Edit')</a>
                            </td>
                            <td>
                                {{$user->name}}
                            </td>
                            <td>{{ucfirst($user->role)}}</td>
                            <td>{{$user->student_code}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone_number}}</td>
                            <td>{{$user->created_at->format('Y-m-d')}}</td>
                        </tr>
                        @endforeach
                    </table>
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
