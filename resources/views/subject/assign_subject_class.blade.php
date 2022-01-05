@extends('layouts.app')
@section('title', __('Subject Summary - Sections'))
@section('content')
<style>
    #cls-sec .panel{
        margin-bottom: 0%;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-6 container" id="main-container">
            <br>
            <h4>@lang('Subject Assignments ')</h4>
            <br>
            <table class="table table-bordered">
                <thead>
                    <th class="text-center">Section</th>
                    <th class="text-center">Student Count (Active)</th>
                    <th class="text-center">Subject Count</th>
                    {{-- <th class="text-center">Total Assigned</th> --}}
                    <th class="text-center">View Details</th>
                </thead>
                <tbody>
                    @foreach ($sections->where('subjects_count', '>', 0) as $section)
                        <tr>
                            <td class="text-center">{{$section->class->class_number.$section->section_number}}</td>
                            <td class="text-center">{{($section->students_count > 0)?$section->students_count:'-'}}</td>
                            <td class="text-center">{{$section->subjects_count}}</td>
                            {{-- <td class="text-center">#</td> --}}
                            <td class="text-center">
                                <a role="button" class="btn btn-primary btn-xs" href="{{url('/subject/section/'.$section->id)}}"><i class="material-icons">visibility</i> @lang('View')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
    </div>
</div>
@endsection
