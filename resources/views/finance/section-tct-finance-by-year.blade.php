@extends('layouts.app')

@section('title', __('Course Students - ' . $year))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
            <br>
            <h4>
                @lang('Form') {{$section->class->class_number}}{{$section->section_number}} - {{ $year }}
            </h4>
            <a href="{{url('fees/assigned/'.$year)}}" class="btn btn-sm btn-info"><i class="material-icons">arrow_back</i> @lang('Back to') {{ $year }} @lang('Summary')</a>
            <br>
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
                                        <th class="text-center" scope="col">@lang('Status')</th>
                                        <th class="text-center" scope="col">Total Assigned</th>
                                        <th class="text-center" scope="col">Total Payments</th>
                                        <th class="text-center" scope="col">Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($students as $student)
                                        @php
                                            // Get form_num and group from either student_info or regrecord
                                            $studentInfo = $student->studentInfo()->where('session', $year)->first();
                                            $regrecord = $student->regrecord()->where('session', $year)->first();
                                            $formNum = $studentInfo ? $studentInfo->form_num : ($regrecord ? $regrecord->form_num : 'N/A');
                                            $group = $studentInfo ? $studentInfo->group : null;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $formNum }}</td>
                                            <td class="text-center">{{$student->student_code}}</td>
                                            <td>
                                                <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                            </td>
                                            <td class="text-center">
                                                @if($student->active)
                                                    <span class="badge bg-success">{{ ucfirst($group ?? 'Graduated') }}</span>
                                                @else
                                                    @php
                                                        $inactiveRecord = $student->inactive->first();
                                                        $inactiveType = $inactiveRecord ? $inactiveRecord->type : 'Inactive';
                                                    @endphp
                                                    <span class="badge bg-secondary">
                                                        @lang('Inactive') / {{ ucfirst($inactiveType) }}
                                                    </span>
                                                @endif
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
                                    <th class="text-center" scope="col">@lang('Status')</th>
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    @php
                                        $studentInfo = $student->studentInfo()->where('session', $year)->first();
                                        $regrecord = $student->regrecord()->where('session', $year)->first();
                                        $formNum = $studentInfo ? $studentInfo->form_num : ($regrecord ? $regrecord->form_num : 'N/A');
                                        $group = $studentInfo ? $studentInfo->group : null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $formNum }}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                        </td>
                                        <td class="text-center">
                                            @if($student->active)
                                                <span class="badge bg-success">{{ ucfirst($group ?? 'Graduated') }}</span>
                                            @else
                                                @php
                                                    $inactiveRecord = $student->inactive->first();
                                                    $inactiveType = $inactiveRecord ? $inactiveRecord->type : 'Inactive';
                                                @endphp
                                                <span class="badge bg-secondary">
                                                    @lang('Inactive') / {{ ucfirst($inactiveType) }}
                                                </span>
                                            @endif
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
                                    <th class="text-center" scope="col">@lang('Student Name')</th>
                                    <th class="text-center" scope="col">@lang('Status')</th>
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    @php
                                        $studentInfo = $student->studentInfo()->where('session', $year)->first();
                                        $regrecord = $student->regrecord()->where('session', $year)->first();
                                        $formNum = $studentInfo ? $studentInfo->form_num : ($regrecord ? $regrecord->form_num : 'N/A');
                                        $group = $studentInfo ? $studentInfo->group : null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $formNum }}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                        </td>
                                        <td class="text-center">
                                            @if($student->active)
                                                <span class="badge bg-success">{{ ucfirst($group ?? 'Graduated') }}</span>
                                            @else
                                                @php
                                                    $inactiveRecord = $student->inactive->first();
                                                    $inactiveType = $inactiveRecord ? $inactiveRecord->type : 'Inactive';
                                                @endphp
                                                <span class="badge bg-secondary">
                                                    @lang('Inactive') / {{ ucfirst($inactiveType) }}
                                                </span>
                                            @endif
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
                                    <th class="text-center" scope="col">@lang('Student Name')</th>
                                    <th class="text-center" scope="col">@lang('Status')</th>
                                    @if(count($feeTypes))
                                        @foreach($feeTypes as $type)
                                            <th class="text-center">{{$type->name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    @php
                                        $studentInfo = $student->studentInfo()->where('session', $year)->first();
                                        $regrecord = $student->regrecord()->where('session', $year)->first();
                                        $formNum = $studentInfo ? $studentInfo->form_num : ($regrecord ? $regrecord->form_num : 'N/A');
                                        $group = $studentInfo ? $studentInfo->group : null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $formNum }}</td>
                                        <td class="text-center">{{$student->student_code}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                        </td>
                                        <td class="text-center">
                                            @if($student->active)
                                                <span class="badge bg-success">{{ ucfirst($group ?? 'Graduated') }}</span>
                                            @else
                                                @php
                                                    $inactiveRecord = $student->inactive->first();
                                                    $inactiveType = $inactiveRecord ? $inactiveRecord->type : 'Inactive';
                                                @endphp
                                                <span class="badge bg-secondary">
                                                    @lang('Inactive') / {{ ucfirst($inactiveType) }}
                                                </span>
                                            @endif
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
        <div class="col-md-2">
            <br>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: white; text-align: center;">
                    <h5><i class="material-icons" style="font-size: 16px; vertical-align: middle;">class</i> Form Navigation</h5>
                </div>
                <div class="panel-body" style="padding: 10px;">
                    @if($sections->count())
                        <div class="list-group">
                            @foreach($sections as $s)
                                <a href="{{ url('fees/section/' . $s->id . '/year/' . $year) }}" 
                                   class="list-group-item {{ $s->id == $section->id ? 'active' : '' }}" 
                                   style="padding: 10px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                                    <span>{{ $s->class->class_number . $s->section_number }}</span>
                                    <span class="badge {{ $s->id == $section->id ? 'badge-light' : 'badge-primary' }}">
                                        {{ $studentCountList['total'][$s->id] ?? 0 }}
                                </a>
                            @endforeach
                        </div>
                    @endif
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
