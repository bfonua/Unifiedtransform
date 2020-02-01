@extends('layouts.app')

@section('title', __('Prefects'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
            <br>
            <h4>
                Prefects {{now()->year}}
            </h4>
            <div class="panel panel-default">
                @if($prefects->first())
                    <div class="panel panel-default">
                        <table id="myTable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center" scope="col">@lang('#')</th>
                                <th class="text-center" scope="col">@lang('TCT ID')</th>
                                {{-- <th class="text-center" scope="col">@lang('Status')</th> --}}
                                <th class="text-center" scope="col">@lang('Student Name')</th>
                                <th class="text-center" scope="col">@lang('Form')</th>
                                <th class="text-center" scope="col">@lang('House')</th>
                                {{-- <th scope="col">@lang('Grade History')</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($prefects as $student)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}
                                    <td class="text-center">
                                        {{$student->tct_id}}
                                    </td>
                                    <td>
                                        <a href="{{url('user/'.$student->student->student_code)}}">{{$student->student->given_name.' '.$student->student->lst_name}}</a>
                                            @if($student->group == "Head Prefect")
                                                - Head Prefect
                                            @endif
                                    </td>
                                    <td class="text-center">
                                        {{$student->section->class->class_number.$student->section->section_number}}
                                    </td>
                                    <td class="text-center">
                                        {{$student->house->house_abbrv}}
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
