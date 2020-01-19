@extends('layouts.app')

@section('title', __('Unassigned'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>

        <div class="col-md-10" id="main-container">
            <br>
            <h4>Unassigned Students - {{now()->year}}</h4>
            <br>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-white bg-danger">{{$error}}</div>
                @endforeach
                <br>
            @endif         
            <div class="panel panel-default">
                @if($unassigned->first())
                    <table id="myTable" class='table'>
                        <thead>
                            <th class="text-center">#</th>
                            <th class="text-center">TCT ID</th>
                            <th class="text-center">Full Name</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Form</th>
                            <th class="text-center">House</th>
                            <th class="text-center">Assign</th>
                        </thead>
                        <tbody>
                            @foreach($unassigned as $unassign)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td class="text-center">{{$unassign->student->student_code}}</td>
                                    <td>
                                        <a href="{{url('user/'.$unassign->student->student_code)}}">{{($unassign->student->name == '')?$unassign->student->given_name.' '.$unassign->student->lst_name:$unassign->student->name}}</a>
                                    </td>
                                    <td class="text-center">{{$unassign->category_id}}</td>
                                    <td>{{($unassign->student->active)?'Active / '.ucfirst($unassign->group):'Inactive'}}</td>
                                    <td class="text-center">{{$unassign->section->class->class_number}}{{$unassign->section->section_number}} (#{{$unassign->form_num}})</td>
                                    <td class="text-center">{{$unassign->house->house_abbrv}}</td>
                                    <td class="text-center">
                                        <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                            {{csrf_field()}}
                                            <input type="hidden" value="{{$unassign->student->id}}" name="user_id">
                                            <input type="hidden" value="{{$unassign->session}}" name="session">
                                            <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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