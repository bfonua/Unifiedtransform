@extends('layouts.app')

@section('title', __('House Students'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
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
                                        @if($student->section)
                                            <a href="{{ url('section/tct_students/' . $student->section->id . '?section=1') }}">
                                                {{$student->section->class->class_number}}{{$student->section->section_number}}
                                            </a>
                                        @else
                                            -
                                        @endif
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
        <div class="col-md-2">
            <br>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: white; text-align: center;">
                    <h5><i class="material-icons" style="font-size: 16px; vertical-align: middle;">house</i> House Navigation</h5>
                </div>
                <div class="panel-body" style="padding: 10px;">
                    @if($houses->count())
                        <div class="list-group">
                            @foreach($houses as $h)
                                <a href="{{ url('house/tct_students/' . $h->id) }}" 
                                   class="list-group-item {{ $h->id == $house->id ? 'active' : '' }}" 
                                   style="padding: 10px 15px; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                                    <span>{{ $h->house_name }}</span>
                                    <span class="badge {{ $h->id == $house->id ? 'badge-light' : 'badge-primary' }}">
                                        {{ $studentCountHouse[$h->id] ?? 0 }}
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
                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
