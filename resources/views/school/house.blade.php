@extends('layouts.app')

@section('title', __('All Houses'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>

        <div class="col-md-8" id="main-container">
            <br>
            <h4>@lang('All Houses')</h4>
            <a href="{{url('students/export/house')}}" class="btn btn-sm btn-success"><i class="material-icons">import_export</i> Export all houses</a>
            @include('layouts.master.add-house-form') <!--NEW HOUSE BUTTON -->
            <hr>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div>{{$error}}</div>
                @endforeach
                <br>
            @endif
   

            <table id="house_table" class='table'>
                <thead>
                    <th class="text-center">House Name</th>
                    <th class="text-center">Student Count</th>
                    <th class="text-center">View Students</th>
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                    <th class="text-center">Edit House</th>
                    @endif
                </thead>
                <tbody>
                    @foreach ($houses as $house)
                        <tr>
                            <td>{{$house->house_name.' ('.$house->house_abbrv.')'}}</td>
                            <td class="text-center">{{($house->current_session_students_count == 0)?'-':$house->current_session_students_count}}</td>
                            <td class="text-center">
                                <a role="button" class="btn btn-primary btn-xs" href="{{url('house/tct_students/'.$house->id)}}"><i class="material-icons">visibility</i> @lang('View Students')</a>
                            </td>
                            @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                            <td class="text-center">
                                @include('layouts.master.edit-house-form')
                            </td>
                            @endif
                        </tr>
                    @endforeach


                </tbody>

            </table>


        </div>
    </div>
</div>
@endsection
