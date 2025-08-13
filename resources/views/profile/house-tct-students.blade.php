@extends('layouts.app')

@section('title', __('House Students'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-7" id="main-container">
            <br>
            <h4>
                {{$house->house_name}}
            </h4>
            <div class="panel panel-default">
              @if($students->first())
                <div class="panel-body table-responsive">
                    <table id="myTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" scope="col">@lang('#')</th>
                            <th class="text-center" scope="col">@lang('TCT ID')</th>
                            <th class="text-center" scope="col">@lang('Status')</th>
                            <th class="text-center" scope="col">@lang('Student Name')</th>
                            <th class="text-center" scope="col">@lang('Form')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td class="text-center">{{$student->tct_id}}</td>
                                    <td class="text-center">
                                        @if(optional($student->student)->active)
                                            <span class="badge bg-success">{{ ucfirst($student->group ?? '') }}</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                @lang('Inactive') /
                                                {{ ucfirst(optional($student->student)->inactiveNow($student->session)->first()->type ?? '') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{url('user/'.$student->student->student_code)}}">{{$student->student->given_name.' '.$student->student->lst_name}}</a>
                                    </td>
                                    <td class="text-center">
                                        {{$student->section->class->class_number}}{{$student->section->section_number}}
                                    </td>

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
