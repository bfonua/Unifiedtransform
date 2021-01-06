@extends('layouts.app')

@section('title', __('Forms by Year'))

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
                <h4>Forms by Year</h4>
                <hr>
                <div class="panel panel-default container col-md-6" id="cls-sec">
                    <div class="panel panel-default">
                        {{-- <h5>Forms / Classes</h5> --}}
                        <div class="page-panel-title" role="tab" id="headers">
                            <div class="row">
                                <div class="col-md-2 text-center"><h5>Year</h5></div>
                                <div class="col-md-3 text-center"><h5>Number of Sections</h5></div>
                                <div class="col-md-3 text-center"><h5>View Sections</h5></div>
                                {{-- <div class="col-md-2 text-center"><h5>Edit</h5></div> --}}
                            </div>
                        </div>
                    </div>
                    @if(isset($list))
                        @foreach ($list as $year => $section)
                            <div class="panel panel-default">
                                <div class="page-panel-title" role="tab" id="heading{{$year}}">
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            {{ $year }}
                                        </div>
                                        <div class="col-md-3">
                                            @php
                                                $output = count($list[$year]['sections']);
                                                $msg = (($output == 0)? '-': (($output == 1)? '1 section': $output.' sections'));
                                            @endphp
                                            <h6 class='text-center'>{{$msg}} </h6>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <a class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$year}}" aria-expanded="false" aria-controls="collapse{{$year}}"><small><b>@lang('Click to view') <i class="material-icons">keyboard_arrow_down</i></b></small></a>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse{{$year}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$year}}">
                                    <div class="panel-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">@lang('Section Name')</th>
                                                    <th class="text-center">@lang('Number of Students')</th>
                                                    <th class="text-center">@lang('View')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($list[$year]['sections'] as $section_id)
                                                    @php
                                                        $sectionReg = \App\Section::find($section_id)    
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $sectionReg->class->class_number }}{{ $sectionReg->section_number }}
                                                        </td>
                                                        <td class="text-center">
                                                            -
                                                        </td>
                                                        <td class="text-center">
                                                            <a class="btn btn-primary btn-xs" href="">View</a>
                                                        </td>
                                                    </tr>

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
