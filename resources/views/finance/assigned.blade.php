@extends('layouts.app')

@section('title', __('Fee Summary - Sections'))

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
        <div class="col-md-7 container" id="main-container">
            <h4>@lang('Assignments and Payments')</h4>
            <br>
            <a href="{{url('#')}}" class="btn btn-sm btn-success disabled"><i class="material-icons">import_export</i> Export summary</a>
            {{-- @include('layouts.master.add-class-form') <!--NEW FORM BUTTON --> --}}
            <hr>
            <table class="table">
                <thead>
                    <th class="text-center">Section</th>
                    <th class="text-center">Student Count</th>
                    <th class="text-center">Total Assigned</th>
                    <th class="text-center">Total Payments</th>
                    <th class="text-center">Remaining</th>
                    <th class="text-center">View Details</th>
                </thead>
                <tbody>

                @php
                    function numberformat($amount){
                        if($amount == 0.00){
                            return '-';
                        }elseif ($amount < 0) {
                            return '('.number_format($amount * -1, 2).')';
                        } else{
                            return ($amount == 0.00)?'-':number_format($amount,2);
                        }
                    }
                @endphp
                    @foreach ($sections as $section)
                        <tr>
                            <td class="text-center">{{$section->class->class_number.$section->section_number}}</td>
                            <td class="text-center">{{$studentCount[$section->id]}}</td>
                            <td class="text-center">{{numberformat($classAssign[$section->class->class_number][$section->id])}}</td>
                            <td class="text-center">{{numberformat($sectionPayment[$section->id])}}</td>
                            <td class="text-center">{{numberformat($sectionRemain[$section->id])}}</td>
                            <td class="text-center">
                                <a role="button" class="btn btn-primary btn-xs" href="{{url('/fees/section/'.$section->id)}}"><i class="material-icons">visibility</i> @lang('View')</a>
                            </td>
                        </tr>
                        
                    @endforeach

                </tbody>
            </table>



          </div>
    </div>
</div>
@endsection
