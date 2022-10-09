@extends('layouts.app')

@section('title', __('Inactive Students'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
            <br>
            <h4>
                Inactive Students {{now()->year}}
            </h4>
            <div class="panel panel-default">
                @if($inactive->first())
                    <div class="panel panel-default">
                        <table id="myTable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center" scope="col">@lang('#')</th>
                                <th class="text-center" scope="col">@lang('TCT ID')</th>
                                <th class="text-center" scope="col">@lang('Student Name')</th>
                                <th class="text-center" scope="col">@lang('Type')</th>
                                <th class="text-center" scope="col">@lang('Form')</th>
                                <th class="text-center" scope="col">@lang('House')</th>
                                <th class="text-center" scope="col">Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- @foreach($inactive as $student)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}
                                    <td class="text-center">
                                        {{$student->studentInfo->tct_id}}
                                    </td>
                                    <td>
                                        <a href="{{url('user/'.$student->student_code)}}">{{$student->given_name.' '.$student->lst_name}}</a>
                                    </td>
                                    <td class="text-center">
                                        {{
                                            ucfirst($student->inactiveNow($maxSession)->orderBy('id', 'desc')->first()->type)
                                        }}
                                    </td>
                                    <td class="text-center">
                                        {{$student->studentInfo->section->class->class_number.$student->studentInfo->section->section_number}}
                                    </td>
                                    <td class="text-center">
                                        {{$student->studentInfo->house->house_abbrv}}
                                    </td>
                                </tr>
                            @endforeach --}}
                                @foreach ($inactive as $student)
                                    @if($student->users)
                                    <tr>
                                        <td class="text-center">{{$loop->iteration}}</td>
                                        <td class="text-center">{{$student->users->studentInfo->tct_id}}</td>
                                        <td>
                                            <a href="{{url('user/'.$student->users->student_code)}}">{{$student->users->given_name.' '.$student->users->lst_name}}</a>
                                        </td>
                                        <td class="text-center">
                                            {{ ucfirst($student->type) }}
                                        </td>
                                        <td class="text-center">
                                            {{$student->users->studentInfo->section->class->class_number.$student->users->studentInfo->section->section_number}}
                                        </td>
                                        <td class="text-center">
                                            {{$student->users->studentInfo->house->house_abbrv}}
                                        </td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" href="{{url('tct_delete_student/'.$student->users->id)}}"><i class="material-icons">delete</i> @lang('Delete Record')</a>
                                        </td>
                                    </tr>
                                    @endif
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
