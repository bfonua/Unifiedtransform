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
                @lang('Form') {{$section->class->class_number}}{{$section->section_number}} - Subject Assign
            </h4>
            <br>
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
                                        @for ($i = 0 ; $i < $section->class->optionCount ; $i++)
                                            <th class="text-center" scope="col">Option {{$i+1}}</th>
                                        @endfor
                                        <th class="text-center" scope="col">Edit</th>
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
                                                @if(!$student->subjectAssigned->first())
                                                    <i class="material-icons pull-right">warning</i> 
                                                @endif
                                                </a>
                                            </td>
                                            @for ($i = 0 ; $i < $section->class->optionCount ; $i++)
                                                <td class="text-center">{{($q = $student->subjectAssigned->where('option', $i+1)->first())? $q->subject->name: '-'}}</td>
                                            @endfor
                                            <td class="text-center">
                                                {{-- <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#feeModal"><i class="material-icons">edit</i></button> --}}
                                                
                                                @component('components.fee-type-form', [
                                                    'buttonTitle' => '',
                                                    'modal_name' => 'reassignSubject'.$student->id,
                                                    'title' => 'Re-assign Optional Subjects',
                                                    'put_method' => '',
                                                    'url' => url("subject/reassign"),
                                                ])
                                                    @slot('buttonType')
                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#reassignSubject{{$student->id}}"><i class="material-icons">edit</i>  
                                                    @endslot
                                                    @slot('form_content')
                                                        <input type="hidden" value="{{now()->year}}" name="session">
                                                        <input type="hidden" value="{{$student->id}}" name="user_id">
                                                        @foreach(range(0,$section->class->optionCount-1) as $i)
                                                            <div class="row form-group">
                                                                <label for="type" class="col-sm-3 control-label">Option {{$i+1}}</label>
                                                                <div class="col-sm-5">
                                                                    @if($subjectClass->first())
                                                                    <select id="option{{$i+1}}" class="form-control" name="option{{$i+1}}">
                                                                        <option value="">N/A</option>
                                                                        @foreach ($subjectClass as $sub)
                                                                            <option value="{{$sub->subject_id}}"
                                                                                @if($q2 = $student->subjectAssigned->where('option', $i+1)->first())
                                                                                    @if ($q2->subject_id == $sub->subject_id)
                                                                                        selected = "selected"
                                                                                    @endif
                                                                                @endif
                                                                                >
                                                                                {{$sub->subject->name}}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <br>
                                                        <div class="row form-group">
                                                            <label for="session" class="col-sm-3 control-label">Session</label>
                                                            <div class="col-sm-4">
                                                                <input id = "session" name="session" class="form-control" value="{{now()->year}}">
                                                            </div>
                                                        </div>
                                                    @endslot
                                                @endcomponent
                                            </td>
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

{{-- @section('jsFiles')
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
@endsection --}}