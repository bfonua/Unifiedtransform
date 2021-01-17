@extends('layouts.app')

@section('title', __('Subjects'))

@section('content')
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        
        <div class="col-md-10">
            <br>
            <h4>@lang('All Subjects')</h4>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-white bg-danger">{{$error}}</div>
                    {{-- <span class="error">{{ $error}}</span> --}}
                @endforeach
                <br>
            @endif
            
            @include('layouts.master.add-subject-form') <!--NEW FORM BUTTON -->
            <hr>
            <div class="container col-md-8" id="cls-sec">
                <div class="panel panel-default">
                    <h4>Active Subjects</h4>
                    <br>
                    @if($activeSubjects->first())
                        <table id="subject" class="table">
                            <thead>
                                <th class="text-center">#</th>
                                <th class="text-left">Name</th>
                                @foreach ($classes as $class)
                                    <th class="text-center">{{ $class->class_number }}</th>
                                @endforeach
                                <th class="text-center">Edit</th>
                            </thead>
                            <tbody>
                                @foreach ($activeSubjects as $sub)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $sub->name}}</td>
                                        @php
                                            $subOptions = [];
                                        @endphp
                                        @foreach ($classes as $class)
                                            @php
                                                $subClass = \App\SubjectClass::where([
                                                    'subject_id' => $sub->id,
                                                    'class_id' => $class->id,
                                                    'active' => 1,
                                                ])->get();
                                            @endphp
                                            @if($subClass->first())
                                                <td class="text-center"><i class="material-icons">done</i></td>
                                            @else
                                                <td class="text-center">-</td>
                                            @endif
                                        @endforeach
                                        <td class="text-center">
                                            @include('layouts.master.edit-subject-form')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        No Related Data found
                    @endif
                </div>
            </div>
            <div class="container col-md-4" id="cls-sec">
                <div class="panel panel-default">
                    <h4>Inactive Subjects</h4>
                    <br>
                    @if($inactiveSubjects->first())
                        <table id="inactive_subject" class="table">
                            <thead>
                                <th class="text-center">#</th>
                                <th class="text-left">Name</th>
                                <th class="text-center">Session</th>
                                <th class="text-center">Edit</th>
                            </thead>
                            <tbody>
                                @foreach($inactiveSubjects as $sub)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-left">{{ $sub->name }}</td>
                                        <td class="text-center">{{ $sub->session }}</td>
                                        <td class="text-center">
                                            @include('layouts.master.edit-subject-form')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        No Related Data found
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
