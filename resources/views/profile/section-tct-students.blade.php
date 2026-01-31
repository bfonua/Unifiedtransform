@extends('layouts.app')

@section('title', __('Forms and Sections'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
            <br>
            <h4>
                @lang('Form') {{$section->class->class_number}}{{$section->section_number}}
            </h4>
            <div class="panel panel-default">
              @if($students->first())
                <div class="panel-body">
                    <table id="myTable" class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="text-center" scope="col">@lang('#')</th>
                            <th class="text-center" scope="col">@lang('TCT ID')</th>
                            <th class="text-center" scope="col">@lang('Status')</th>
                            <th class="text-center" scope="col">@lang('Student Name')</th>
                            <th class="text-center" scope="col">@lang('House')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td class="text-center" scope="row">{{$student->studentInfo->form_num ?? '-'}}</td>
                                <td class="text-center">{{$student->student_code ?? '-'}}</td>
                                <td class="text-center">
                                    @if($student->active == "1")
                                        <span class="badge bg-success">{{ ucfirst($student->studentInfo->group ?? '') }}</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            @lang('Inactive') /
                                            {{ ucfirst(optional($student->inactiveNow($student->studentInfo->session)->first())->type) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('user/'.$student->student_code) }}">
                                        {{ $student->given_name.' '.$student->lst_name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if($student->studentInfo && $student->studentInfo->house)
                                        <a href="{{ url('house/tct_students/' . $student->studentInfo->house->id . '?section=1') }}">
                                            {{$student->studentInfo->house->house_abbrv}}
                                        </a>
                                    @else
                                        -
                                    @endif
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
                                <a href="{{ url('section/tct_students/' . $s->id . '?section=1') }}" 
                                   class="list-group-item {{ $s->id == $section->id ? 'active' : '' }}" 
                                   style="padding: 10px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                                    <span>{{ $s->class->class_number . $s->section_number }}</span>
                                    <span class="badge {{ $s->id == $section->id ? 'badge-light' : 'badge-primary' }}">
                                        {{ $studentCountList['total'][$s->id] ?? 0 }} ({{ $studentCountList['active'][$s->id] ?? 0 }})
                                    </span>
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
            $('#myTable').DataTable({
                paging: false, // Enable paging for large lists
                searching: true, // Enable search box
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
