@extends('layouts.app')

@section('title', __('TCTNET Home'))

@section('content')
<style>
    .badge-download {
        background-color: transparent !important;
        color: #464443 !important;
    }
    .list-group-item-text{
      font-size: 12px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="" style="border-top: 0px;">
                <div class="">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{-- <div class="row">
                        <div class="page-panel-title">@lang('Dashboard')</div>
                        <div class="col-sm-2">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-header">@lang('Students') - <b>{{$totalStudents}}</b></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-header">@lang('Teachers') - <b>{{$totalTeachers}}</b></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card text-white bg-dark mb-3">
                                <div class="card-header">@lang('Types of Books In Library') - <b>{{$totalBooks}}</b></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card text-white bg-info mb-3">
                                <div class="card-header">Forms - <b>{{count($sections)}}</b></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-header">@lang('Houses') - <b>{{$housesCount}}</b></div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="container col-md-12">
                            <br>
                            <div class="" style="">
                                <div class="">
                                    <h2>@lang('Welcome to') 
                                        @if(Auth::user()->school->name == "Tupou College")
                                             TCTNET
                                        @else
                                            {{Auth::user()->school->name}}
                                        @endif
                                    </h2>
                                    <div class="panel panel-default">
                                        {{-- <div class="page-panel-title">
                                            Mission Statement
                                        </div> --}}
                                        <div class="panel-body">
                                            <h4>{{now()->year}} Administration</h4>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    {{-- <div class="col-sm-7"> --}}
                                                    <div class="card bg-light mb-3">
                                                        <div class="card-header">Student Count</div>
                                                        <div class="card-body">
                                                            <table class="table table-striped table-condensed">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><b>Total Registered</b></td>
                                                                        <td class="text-right">{{$totalStudents}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Active</td>
                                                                        <td class="text-right">{{$totalActive}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Inactive</td>
                                                                        <td class="text-right">{{$inactive = $totalStudents - $totalActive}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <hr>
                                                            <table class="table table-striped table-condensed">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><b>Total Inactive</b></td>
                                                                        <td class="text-right">{{$inactive}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Removed</td>
                                                                        <td class="text-right">{{$inactiveOutput['removed']}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Withdrawn</td>
                                                                        <td class="text-right">{{$inactiveOutput['withdrawn']}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Suspended</td>
                                                                        <td class="text-right">{{$inactiveOutput['suspended']}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Expelled</td>
                                                                        <td class="text-right">{{$inactiveOutput['expelled']}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <hr>
                                                            <table class="table table-striped table-condensed">
                                                                <tbody>
                                                                    <tr>
                                                                        
                                                                        <td><b>Total Archived</b></td>
                                                                        <td class="text-right">{{\App\StudentInfo::where('session',"<",now()->year)->count()}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card text-center bg-light mb-3">
                                                        <div class="card-header">Form Distribution</div>
                                                        <div class="card-body pre-scrollable">
                                                            @if($sections->first())
                                                                <table class="table"> 
                                                                    <thead>
                                                                        <th class="text-center">Form</th>
                                                                        <th class="text-center">Active</th>
                                                                        <th class="text-center">Inactive</th>
                                                                        <th class="text-center">Total</th>
                                                                    </thead> 
                                                                    @foreach($sections as $section)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <a href="{{url('section/tct_students/'.$section->id.'?section=1')}}"> {{$section->class->class_number.$section->section_number}} </a>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ $studentCountList['active'][$section->id] }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ $studentCountList['total'][$section->id] - $studentCountList['active'][$section->id] }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ $studentCountList['total'][$section->id] }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card text-center bg-light mb-3">
                                                        <div class="card-header">House Distribution</div>
                                                        <div class="card-body pre-scrollable">
                                                            @if($houses->first())
                                                                <table class="table"> 
                                                                    <thead>
                                                                        <th class="text-center">House</th>
                                                                        <th class="text-center">Count</th>
                                                                    </thead> 
                                                                    @foreach($houses as $house)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <a href="{{url('house/tct_students/'.$house->id.'?section=1')}}">{{$house->house_name}}</a>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{$studentCountHouse[$house->id]}}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>  
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    {{-- <div class="row panel panel-default">
                                                        <div class="page-panel-title text-center">@lang('Notices')</div>
                                                        <div class="panel-body pre-scrollable">
                                                            @if(count($notices) > 0)
                                                            <div class="list-group">
                                                                @foreach($notices as $notice)
                                                                <a href="{{url($notice->file_path)}}" class="list-group-item" download>
                                                                    <i class="badge badge-download material-icons">
                                                                        get_app
                                                                    </i>
                                                                    <h5 class="list-group-item-heading">{{$notice->title}}</h5>
                                                                    <p class="list-group-item-text">@lang('Published at'):
                                                                        {{$notice->created_at->format('M d Y h:i:sa')}}</p>
                                                                </a>
                                                                @endforeach
                                                            </div>
                                                            @else
                                                            @lang('No New Notice')
                                                            @endif
                                                        </div>
                                                    </div> --}}
                                                    <div class="row panel panel-default">
                                                        <div class="page-panel-title text-center">Links</div>
                                                        <div class="panel-body text-center">
                                                            <a href="#">New Application</a><br>
                                                            <a data-toggle="tooltip" data-placement="bottom" title="Please search Student ID to register">
                                                                Register Old Students
                                                            </a><br>
                                                            <a href="{{url('students/export/tct')}}">Export Class Lists</a><br>
                                                            <br>
                                                            <a href="{{url('prefects/tct_students')}}">View Prefects</a><br>
                                                            <a data-toggle="tooltip" data-placement="bottom" title="Please use Student profile page">
                                                                Set Student Fees
                                                            </a><br>
                                                            <a href="{{url('fees/fee_types')}}">View Fee Types</a><br>
                                                            <a href="{{url('fees/fee_channel')}}">View Fee Channels</a><br>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <h4>{{now()->year}} Finance</h4>
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="card bg-light mb-3">
                                                        <div class="card-header">Total Fees Amount</div>
                                                        <div class="card-body">
                                                            <table class="table table-condensed">
                                                                <thead>
                                                                    <th class='text-center'>Type</th>
                                                                    <th class='text-right'>Term 1</th>
                                                                    <th class='text-right'>Term 2</th>
                                                                    <th class='text-right'>Term 3</th>
                                                                    <th class='text-right'>Term 4</th>
                                                                    <th class='text-right'>Late</th>
                                                                    <th class='text-right'>Total</th>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($feeArr as $fee => $feeType)
                                                                        <tr>
                                                                            <td>{{$fee}}</td>
                                                                            <td class="text-right">{{number_format($feeType['Term 1'],2)}}</td>
                                                                            <td class="text-right">{{number_format($feeType['Term 2'],2)}}</td>
                                                                            <td class="text-right">{{number_format($feeType['Term 3'],2)}}</td>
                                                                            <td class="text-right">{{number_format($feeType['Term 4'],2)}}</td>
                                                                            <td class="text-right">{{number_format($feeType['Late Registration'],2)}}</td>
                                                                            <td class="text-right">{{number_format($feeType['total'],2)}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="panel panel-default">
                                <div class="page-panel-title">@lang('Active Exams')</div>
                                <div class="panel-body">
                                    @if(count($exams) > 0)
                                    <table class="table">
                                        <tr>
                                            <th>@lang('Exam Name')</th>
                                            <th>@lang('Notice Published')</th>
                                            <th>@lang('Result Published')</th>
                                        </tr>
                                        @foreach($exams as $exam)
                                        <tr>
                                            <td>{{$exam->exam_name}}</td>
                                            <td>{{($exam->notice_published === 1)?__('Yes'):__('No')}}</td>
                                            <td>{{($exam->result_published === 1)?__('Yes'):__('No')}}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                    @else
                                    @lang('No Active Examination')
                                    @endif
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-sm-4">
                            <div class="panel panel-default">
                                <div class="page-panel-title">@lang('Events')</div>
                                <div class="panel-body pre-scrollable">
                                    @if(count($events) > 0)
                                    <div class="list-group">
                                        @foreach($events as $event)
                                        <a href="{{url($event->file_path)}}" class="list-group-item" download>
                                            <i class="badge badge-download material-icons">
                                                get_app
                                            </i>
                                            <h5 class="list-group-item-heading">{{$event->title}}</h5>
                                            <p class="list-group-item-text">@lang('Published at'):
                                                {{$event->created_at->format('M d Y')}}</p>
                                        </a>
                                        @endforeach
                                    </div>
                                    @else
                                    @lang('No New Event')
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="panel panel-default">
                                <div class="page-panel-title">@lang('Routines')</div>
                                <div class="panel-body pre-scrollable">
                                    @if(count($routines) > 0)
                                    <div class="list-group">
                                        @foreach($routines as $routine)
                                        <a href="{{url($routine->file_path)}}" class="list-group-item" download>
                                            <i class="badge badge-download material-icons">
                                                get_app
                                            </i>
                                            <h5 class="list-group-item-heading">{{$routine->title}}</h5>
                                            <p class="list-group-item-text">@lang('Published at'):
                                                {{$routine->created_at->format('M d Y')}}</p>
                                        </a>
                                        @endforeach
                                    </div>
                                    @else
                                    @lang('No New Routine')
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="panel panel-default">
                                <div class="page-panel-title">@lang('Syllabus')</div>
                                <div class="panel-body pre-scrollable">
                                    @if(count($syllabuses) > 0)
                                    <div class="list-group">
                                        @foreach($syllabuses as $syllabus)
                                        <a href="{{url($syllabus->file_path)}}" class="list-group-item" download>
                                            <i class="badge badge-download material-icons">
                                                get_app
                                            </i>
                                            <h5 class="list-group-item-heading">{{$syllabus->title}}</h5>
                                            <p class="list-group-item-text">@lang('Published at'):
                                                {{$syllabus->created_at->format('M d Y')}}</p>
                                        </a>
                                        @endforeach
                                    </div>
                                    @else
                                    @lang('No New Syllabus')
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>

@endsection
