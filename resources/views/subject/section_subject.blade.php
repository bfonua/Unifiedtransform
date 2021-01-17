@extends('layouts.app')

@section('title', __('Classes & Subjects'))

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
                <h4>@lang('Classes and Optional Subjects')</h4>
                <hr>
                <div class="panel panel-default container col-md-6" id="cls-sec">
                    <div class="panel panel-default">
                        <h5>Forms / Classes</h5>
                        <div class="page-panel-title" role="tab" id="headers">
                            <div class="row">
                                <div class="col-md-2 text-center"><h5>Form</h5></div>
                                <div class="col-md-3 text-center"><h5>Number of Subjects</h5></div>
                                <div class="col-md-3 text-center"><h5>View Subjects</h5></div>
                                {{-- <div class="col-md-2 text-center"><h5>Edit</h5></div> --}}
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
                                                $output = $classSubList[$class->id]['subRecord']->count();
                                                $msg = (($output == 0)? '-': (($output == 1)? '1 subject': $output.' subjects'));
                                            @endphp
                                            <h6 class='text-center'>{{ $msg }}</h6>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$class->id}}" aria-expanded="false" aria-controls="collapse{{$class->id}}"><small><b>@lang('Click to view') <i class="material-icons">keyboard_arrow_down</i></b></small></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="collapse{{$class->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$class->id}}">
                                    <div class="panel-body">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Subject Name</th>
                                                    <th class="text-center">Sutudent Count</th>
                                                    <th class="text-center">View Students</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($classSubList[$class->id]['subRecord'] as $sub)
                                                    @php
                                                        $output = $classSubList[$class->id]['subCount'][$sub->subject_id];
                                                        $msg = ($output == "0")? '-': $output;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{$loop->iteration}}</td>
                                                        <td class="text-left">{{$sub->subject->name}}</td>
                                                        <td class="text-center">{{$output}}</td>
                                                        <td class="text-center">
                                                            <a role="button" class="btn btn-primary btn-xs" href="{{url('subject/tct_students/'.$sub->id)}}"><i class="material-icons">visibility</i> @lang('View Students')</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
