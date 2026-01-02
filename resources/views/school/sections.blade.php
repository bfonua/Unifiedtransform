@extends('layouts.app')

@section('title', __('Classes'))

@section('content')

    @if($errors->any())
        @foreach ($errors->all() as $error)
            <div class="bg-danger text-white">{{$error}}</div>
        @endforeach
    @endif

    <style>
        #cls-sec .panel{
            margin-bottom: 0%;
        }
    </style>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2" id="side-navbar">
                @include('layouts.leftside-menubar')
            </div>
            <div class="col-md-10" id="main-container">
                <br>
                <h4>@lang('Active Classes & Sections')</h4>
                <a href="{{url('students/export/tct')}}" class="btn btn-sm btn-success"><i class="material-icons">import_export</i> Export Class List</a>
                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                    <a href="{{url('students/all_reg')}}" class="btn btn-sm btn-primary"><i class="material-icons">import_export</i> Export Reg Info</a>
                    @include('layouts.master.add-class-form') <!--NEW FORM BUTTON -->
                    {{-- @include('layouts.master.add-department-form') --}}
                @endif
                <hr>
                <div class="panel panel-default container col-md-6" id="cls-sec">
                    <div class="panel panel-default">
                        <h5>Forms / Classes</h5>
                        <div class="page-panel-title" role="tab" id="headers">
                            <div class="row">
                                <div class="col-md-2 text-center"><h5>Form</h5></div>
                                <div class="col-md-3 text-center"><h5>Number of Sections</h5></div>
                                <div class="col-md-3 text-center"><h5>View Sections</h5></div>
                                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                    <div class="col-md-2 text-center"><h5>Edit</h5></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(count($classes) > 0)
                        @foreach ($classes as $class)
                            <div class="panel panel-default">
                                <div class="page-panel-title" role="tab" id="heading{{$class->id}}">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$class->id}}" aria-expanded="false" aria-controls="collapse{{$class->id}}">{{$class->class_number}} {{ucfirst($class->group)}}</a>
                                            </div>
                                            <div class="col-md-3">
                                                @php
                                                    $output = $class->sections_count;
                                                    $msg = (($output == 0)? '-': (($output == 1)? '1 section': $output.' sections'));
                                                @endphp
                                                <h6 class='text-center'>{{$msg}} </h6>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$class->id}}" aria-expanded="false" aria-controls="collapse{{$class->id}}"><small><b>@lang('Click to view') <i class="material-icons">keyboard_arrow_down</i></b></small></a>
                                            </div>
                                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                                <div class="col-md-2 text-center">
                                                    @include('layouts.master.edit-class-form')
                                                </div>
                                            @endif
                                        </div>
                                </div>
                                <div id="collapse{{$class->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$class->id}}">
                                    <div class="panel-body">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center">@lang('Section Name')</th>
                                                    @if(isset($_GET['att']) && $_GET['att'] == 1)
                                                        <th class="text-center">@lang('View Today\'s Attendance')</th>
                                                        <th class="text-center">@lang('View Each Student\'s Attendance')</th>
                                                        <th class="text-center">@lang('Give Attendance')</th>
                                                    @endif
                                                    @if(isset($_GET['course']) && $_GET['course'] == 1)
                                                    <th class="text-center">@lang('Active')</th>
                                                    <th class="text-center">@lang('Student Count')</th>
                                                    <th class="text-center">@lang('View Students')</th>
                                                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                                        <th class="text-center">@lang('Edit')</th>
                                                    @endif

                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sections as $section)
                                                    @if($class->id == $section->class_id)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{$section->section_number}}
                                                            </td>
                                                            @if(isset($_GET['course']) && $_GET['course'] == 1)
                                                                <td class="text-center">{{($section->active)?"Yes":"No"}}</td>
                                                                <td class="text-center">{{($section->active)?$section->students_count:'-'}}</td>
                                                                <td class="text-center">
                                                                    <a role="button" class="btn btn-primary btn-xs" href="{{url('section/tct_students/'.$section->id.'?section=1')}}"><i class="material-icons">visibility</i> @lang('View Students')</a>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                                                        @include('layouts.master.edit-sections-form')
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                            @include('layouts.master.create-section-form')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="panel-body">
                            @lang('No Related Data Found.')
                        </div>
                    @endif
                </div>
                {{-- DEPARTMENTS --}}
                {{-- <div class="container col-md-6">
                    <div class="panel panel-default">
                        <h5>Departments</h5>
                        <div class="page-panel-title" role="tab" id="headers">
                            <div class="row">
                                <div class="col-md-2 text-center"><h5>Name</h5></div>
                                <div class="col-md-3 text-center"><h5>Number of Subjects</h5></div>
                                <div class="col-md-3 text-center"><h5>View Subjects</h5></div>
                                <div class="col-md-2 text-center"><h5>Edit</h5></div>
                            </div>
                        </div>
                        @if(count($departments) > 0)
                            @foreach ($departments as $department)
                                <div class="panel panel-default">
                                <div class="page-panel-title" role="tab" id="heading{{$class->id}}">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$department->id}}" aria-expanded="false" aria-controls="collapse{{$department->id}}">{{$department->department_name}}</a>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class='text-center'> # </h6>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$department->id}}" aria-expanded="false" aria-controls="collapse{{$department->id}}"><small><b>@lang('Click to view') <i class="material-icons">keyboard_arrow_down</i></b></small></a>
                                            </div>
                                            <div class="col-md-2 text-center">                    
                                                @include('layouts.master.edit-department-form')
                                            </div>
                                        </div>
                                </div>
                                <div id="collapse{{$department->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$department->id}}">
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="panel-body">
                                @lang('No Department Found.')
                            </div>
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection 
