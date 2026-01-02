@extends('layouts.app')
@section('title', __('Fee Summary - Sections (' . $year . ')'))
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
            <br>
            <h4>@lang('Fee Summary by Form') - {{ $year }}</h4>
            <a href="{{url('fees/assign')}}" class="btn btn-sm btn-info"><i class="material-icons">arrow_back</i> @lang('Back to Current Year')</a>
            <br>
            <br>
            <table class="table table-bordered">
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
                        @php
                            $assign = $section->total_assigned_year ?? 0;
                            $payment = $section->total_paid_year ?? 0;
                            $remain = $assign - $payment;
                        @endphp
                        <tr>
                            <td class="text-center">{{$section->class->class_number.$section->section_number}}</td>
                            <td class="text-center">{{$section->students_count}}</td>
                            <td class="text-center">{{numberformat($assign)}}</td>
                            <td class="text-center">{{numberformat($payment)}}</td>
                            <td class="text-center">{{numberformat($remain)}}</td>
                            <td class="text-center">
                                <a role="button" class="btn btn-primary btn-xs 
                                    @if($assign <= 0)
                                        disabled
                                    @endif
                                " href="{{url('/fees/section/'.$section->id.'/year/'.$year)}}"><i class="material-icons">visibility</i> @lang('View')</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
    </div>
</div>
@endsection
