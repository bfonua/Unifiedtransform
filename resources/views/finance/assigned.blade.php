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
            <a href="{{url('fees/exportAssign')}}" class="btn btn-sm btn-primary"><i class="material-icons">import_export</i> Export Assigned</a>
            <a href="{{url('fees/exportPayment')}}" class="btn btn-sm btn-danger"><i class="material-icons">monetization_on</i> Export Payment</a>
            <a href="{{url('fees/exportRemain')}}" class="btn btn-sm btn-success"><i class="material-icons">monetization_on</i> Export Remaining</a>
            {{-- @include('layouts.master.add-class-form') <!--NEW FORM BUTTON --> --}}
            <hr>
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
                            $assign = $section->totalAssigned()->sum('fees.amount');
                            $payment = $section->payment()->sum('amount');
                            $remain = $assign - $payment;
                        @endphp
                        <tr>
                            <td class="text-center">{{$section->class->class_number.$section->section_number}}</td>
                            <td class="text-center">{{$section->students()->count()}}</td>
                            <td class="text-center">{{numberformat($assign)}}</td>
                            <td class="text-center">{{numberformat($payment)}}</td>
                            <td class="text-center">{{numberformat($remain)}}</td>
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
