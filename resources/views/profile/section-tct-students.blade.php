@extends('layouts.app')

@section('title', __('Course Students'))

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
                    <table id="myTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" scope="col">@lang('#')</th>
                            <th class="text-center" scope="col">@lang('TCT ID')</th>
                            <th class="text-center" scope="col">@lang('Status')</th>
                            <th class="text-center" scope="col">@lang('Student Name')</th>
                            <th class="text-center" scope="col">@lang('House')</th>
                            {{-- <th scope="col">@lang('Grade History')</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td class="text-center">{{$student->studentInfo->form_num}}</td>
                                <td class="text-center">{{$student->student_code}}</td>
                                <td class="text-center">
                                    {{($student->active =="1")?ucfirst($student->studentInfo->group):'Inactive / '.ucfirst($student->inactiveNow($student->studentInfo->session)->first()->type)}}  
                                </td>
                                <td>
                                    <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                </td>
                                <td class="text-center">
                                    {{$student->studentInfo->house->house_abbrv}}
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
@endsection

@section('jsFiles')
    <script>
        $(document).ready(function($){
            $('#myTable').DataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
