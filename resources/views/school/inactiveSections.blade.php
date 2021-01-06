@extends('layouts.app')

@section('title', __('Departments and Classes'))

@section('content')
    {{-- Error Handling --}}
    @if($errors->any())
        @foreach ($errors->all() as $error)
            <div class="bg-danger text-white">{{$error}}</div>
        @endforeach
    @endif
    {{-- Custom CSS Styles --}}
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
                <h4>@lang('Inactive Forms / Classes')</h4>
                <hr>
                <div class="panel panel-default container col-md-6" id="cls-sec">
                    <div class="panel panel-default">
                        <h5>Forms / Classes</h5>
                        <div class="page-panel-title" role="tab" id="headers">
                            <div class="row">
                                <div class="col-md-2 text-center"><h5>Form</h5></div>
                                <div class="col-md-3 text-center"><h5>Number of Sections</h5></div>
                                <div class="col-md-3 text-center"><h5>View Sections</h5></div>
                                <div class="col-md-2 text-center"><h5>Edit</h5></div>
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
                                                $output = $class->active_sections()->count('id');
                                                $msg = (($output == 0)? '-': (($output == 1)? '1 section': $output.' sections'));
                                            @endphp
                                            <h6 class='text-center'>{{$msg}} </h6>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$class->id}}" aria-expanded="false" aria-controls="collapse{{$class->id}}"><small><b>@lang('Click to view') <i class="material-icons">keyboard_arrow_down</i></b></small></a>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @include('layouts.master.edit-class-form')
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse{{$class->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$class->id}}">
                                    <div class="panel-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">@lang('Section Name')</th>
                                                    <th class="text-center">@lang('Active')</th>
                                                    <th class="text-center">@lang('Last Session')</th>
                                                    <th class="text-center">@lang('Edit')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sections as $section)
                                                    @if($class->id == $section->class_id)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{$section->section_number}}
                                                            </td>
                                                            <td class="text-center">{{($section->active)?"Yes":"No"}}</td>
                                                                @php
                                                                    $lastSession = \App\Regrecord::where('form_id', $section->id)
                                                                    ->max('session');
                                                                @endphp
                                                            <td class="text-center">{{ ($lastSession == "")? "-":$lastSession }}</td>
                                                            <td class="text-center">
                                                                @include('layouts.master.edit-sections-form')
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
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
            </div>
        </div>
    </div>
    

@endsection
