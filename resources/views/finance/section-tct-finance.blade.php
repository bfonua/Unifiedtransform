@extends('layouts.app')

@section('title', __('Course Students'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <br>
            <h4>
                @lang('Form') {{$section->class->class_number}}{{$section->section_number}}
            </h4>
            <br>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active " data-toggle="tab" href="#summary">Summary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#assign">Assigned</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#paid">Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#remain">Remaining</a>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Summary --}}
                <div class="tab-pane active" id="summary">
                    <div class="panel panel-default">
                        @if($students->first())
                            <div class="panel-body">
                                <table id="myTable4" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center" scope="col">@lang('#')</th>
                                        <th class="text-center" scope="col">@lang('TCT ID')</th>
                                        <th class="text-center" scope="col">@lang('Student Name')</th>
                                        <th class="text-center" scope="col">Total Assigned</th>
                                        <th class="text-center" scope="col">Total Payments</th>
                                        <th class="text-center" scope="col">Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($students as $student)
                                        {{-- <tr @if(!$student->studentInfo->assigned) class="danger" @endif> --}}
                                        <tr>
                                            <td class="text-center">{{$student->studentInfo->form_num}}</td>
                                            <td class="text-center">{{$student->student_code}}</td>
                                            <td>
                                                <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}
                                                @if(!$student->studentInfo->assigned)
                                                    <i class="material-icons pull-right">warning</i> 
                                                @endif
                                                </a>
                                            </td>
                                            <td class="text-center">{{$studentFees[$student->id]['assign']['total']}}</td>
                                            <td class="text-center">{{$studentFees[$student->id]['payment']['total']}}</td>
                                            <td class="text-center">{{$studentFees[$student->id]['remain']['total']}}</td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="panel-body">
                                @lang('No Related Data Found.')
                            </div>
                        @endif
                    </div>
                </div>
                {{-- ASSIGN SECTION --}}
                <div class="tab-pane" id="assign">
                    <div class="panel panel-default">
                    @if($students->first())
                        <div class="panel-body">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col">@lang('#')</th>
                                    <th class="text-center" scope="col">@lang('TCT ID')</th>
                                    <th class="text-center" scope="col">@lang('Student Name')</th>
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td class="text-center">{{$student->studentInfo->form_num}}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}
                                            @if(!$student->studentInfo->assigned)
                                                <i class="material-icons pull-right">warning</i> 
                                            @endif
                                            </a>
                                        </td>
                                        @if(count($feeTypes))
                                            @foreach($feeTypes as $type)  
                                                <td class="text-center">{{$studentFees[$student->id]['assign'][$type->name]}}</td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="panel-body">
                            @lang('No Related Data Found.')
                        </div>
                    @endif
                    </div>
                </div>
                {{-- PAYMENTS SECTION --}}
                <div class="tab-pane" id="paid">
                    <div class="panel panel-default">
                        @if($students->first())
                        <div class="panel-body">
                            <table id="myTable2" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col">@lang('#')</th>
                                    <th class="text-center" scope="col">@lang('TCT ID')</th>
                                    {{-- <th class="text-center" scope="col">@lang('Status')</th> --}}
                                    <th class="text-center" scope="col">@lang('Student Name')</th>
                                    {{-- <th class="text-center" scope="col">@lang('House')</th> --}}
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td class="text-center">{{$student->studentInfo->form_num}}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}
                                            @if(!$student->studentInfo->assigned)
                                                <i class="material-icons pull-right">warning</i> 
                                            @endif
                                            </a>
                                        </td>
                                        @if(count($feeTypes))
                                            @foreach($feeTypes as $type)  
                                                <td class="text-center">{{$studentFees[$student->id]['payment'][$type->name]}}</td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="panel-body">
                                @lang('No Related Data Found.')
                            </div>
                        @endif
                        </div>
                </div>
                {{-- REMAINING SECTION --}}
                <div class="tab-pane" id="remain">
                    <div class="panel panel-default">
                        @if($students->first())
                        <div class="panel-body">
                            <table id="myTable3" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col">@lang('#')</th>
                                    <th class="text-center" scope="col">@lang('TCT ID')</th>
                                    {{-- <th class="text-center" scope="col">@lang('Status')</th> --}}
                                    <th class="text-center" scope="col">@lang('Student Name')</th>
                                    {{-- <th class="text-center" scope="col">@lang('House')</th> --}}
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td class="text-center">{{$student->studentInfo->form_num}}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}
                                            @if(!$student->studentInfo->assigned)
                                                <i class="material-icons pull-right">warning</i> 
                                            @endif
                                            </a>
                                        </td>
                                        @if(count($feeTypes))
                                            @foreach($feeTypes as $type)  
                                                <td class="text-center">{{$studentFees[$student->id]['remain'][$type->name]}}</td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
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
    </div>
</div>
@endsection

@section('jsFiles')
    <script>
        $(document).ready(function($){
            $('#myTable, #myTable2, #myTable3, #myTable4').DataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
