@extends('layouts.app')

@if(count(array($user)) > 0)
  @section('title', $user->student_code.' - '.$user->given_name)
@endif

@php $userSer = $user; @endphp
@inject('userSer', 'App\Services\User\UserService')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2" id="side-navbar">
                @include('layouts.leftside-menubar')
            </div>
            <div class="col-md-10" id="main-container">
                <div class="panel panel-default">
                    <div class="page-panel-title">
                        <small> @component('components.tct-student-summary',['user'=>$user])
                            @endcomponent 
                        </small>
                    </div>
                    <br>
                    <div class="container col-md-5">
                        <h4>Fees Assigned</h4>
                        @if($assigned > 0)
                            @php                              
                                $lstYear = $user->feesAssigned->max('session');
                                $minYear = "20".substr($user->studentCode,0,2);
                                $years = range($lstYear, $minYear);
                            @endphp
                            <div class="text-center">
                                <h4>{{$session}}<small> - Channel: {{\App\Fee::find($feeList[$session]['fee_id'])->first()->fee_channel->name}}</small></h4>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr class="bg-secondary text-white">
                                        <th scope="col" class="text-center">Session</th>
                                        <th scope="col" class="text-center">Assigned</th>
                                        <th scope="col" class="text-center">Paid</th>
                                        <th scope="col" class="text-center">Remaining</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                        @php 
                                            $total = [
                                                'assign' => 0,
                                                'pay' => 0,
                                                'remain' => 0,
                                            ]; 
                                            if($session < 2020){
                                                $schoolType = ['Term 1', 'Term 2', 'Term 3', 'Term 4'];
                                                $typeIDs = \App\FeeType::whereIn('name', $schoolType)->pluck('id')->toArray();
                                                $allUserFees = \App\Assign::where('user_id', $user->id)
                                                ->where
                                                ('session', $session)
                                                ->pluck('fee_id')->toArray();
                                                $schoolAssign = \App\Fee::find($allUserFees)->whereIn('fee_type_id', $typeIDs)->sum('amount');
                                                $schoolFees = ['School Fees','term1', 'term2', 'term3', 'term4'];
                                                $schoolAmountPaid = \App\PaymentMigrate::where('tct_id', $user->studentInfo->tct_id)
                                                ->whereIn('fee_type', $schoolFees)->where('year', $session)->sum('amount');
                                                $schoolFeesAccrue = 0;
                                            }                                                                                      
                                        @endphp
                                        {{-- {{$schoolAmountPaid}} --}}
                                        @foreach ($feeList[$session]['fee_id'] as $id)
                                            <tr>
                                                <th scope="row" class="text-center">{{$type = \App\Fee::find($id)->fee_type->name}}</th>
                                                <td class="text-center">{{$userSer->numberformat($assign = \App\Fee::find($id)->amount)}}</td>
                                                {{-- Checks if Session is before 2020 - to use old Payments table --}}
                                                @if($session < 2020)
                                                    @if(in_array($type, $schoolType))
                                                        @if($schoolAmountPaid - $schoolFeesAccrue >= $assign)
                                                            <td class="text-center">{{$userSer->numberformat($payment = $assign)}}</td>
                                                        @else
                                                            <td class="text-center">{{$userSer->numberformat($payment = $schoolAmountPaid-$schoolFeesAccrue)}}</td>
                                                        @endif
                                                        @php $schoolFeesAccrue += $payment; @endphp

                                                    @else
                                                        @php $payment = $userSer->getPayment($user->id, $session, $id, 0, $type) @endphp
                                                        <td class="text-center">{{$userSer->numberformat($payment)}}</td>
                                                    @endif
                                                @else
                                                    @php $payment = $userSer->getPayment($user->id, $session, $id);
                                                        // dd($payment);
                                                    @endphp
                                                    <td class="text-center">{{$userSer->numberformat($payment)}}</td>
                                                @endif
                                                <td class="text-center">{{$userSer->numberformat($remain = $assign - $payment)}}</td>
                                                @php
                                                    $total['assign'] += $assign;
                                                    $total['pay'] += $payment;
                                                    $total['remain'] += $remain;
                                                @endphp
                                            </tr>
                                        @endforeach
                                        <style>
                                            .tr-total{
                                                color: #401500;
                                                background-color: #FFDDCC;
                                                border-color: #792700;
                                            }
                                        </style>
                                        <tr class="tr-total">
                                            <strong>
                                            <th class="text-center">TOTAL</th>
                                            <td class="text-center">{{$userSer->numberformat($total['assign'])}}</td>
                                            <td class="text-center">{{$userSer->numberformat($total['pay'])}}</td>
                                            <td class="text-center">{{$userSer->numberformat($total['remain'])}}</td>
                                            </strong>
                                        </tr>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tbody>
        
                            </table>
                        @else
                            <br>
                            No Fees assigned for {{$session}} <br>
                        @endif     
                    </div>
                    <div class="container col-md-5">
                        <h4>Reassign Fees</h4>
                        @if(count(array($user)) > 0)
                            <div class="panel-body">
                                @php $userSer = $user; @endphp
                                @inject('userSer', 'App\Services\User\UserService')
                                @if($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="bg-danger text-white">{{$error}}</div>
                                    @endforeach
                                    <br>
                                @endif
                                <form class="form-horizontal" action="{{url('fees/reassign')}}" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" value="{{$user->id}}" name="user_id">
                                    <input id='year' type="hidden" value="{{$session}}" name="session">
                                    <div class="row form-group">
                                        <label for="channel" class="col-md-4 control-label">@lang('Fee Channel')</label>
                                        <div class="col-md-6">
                                            <select id="channel" class="form-control" name="channel">
                                                @php
                                                    $channels[$session] = \App\FeeChannel::where('session',$session)->get();
                                                @endphp
                                                    <option value="0">Select Channel</option>
                                                @foreach ($channels[$session] as $channel)
                                                    <option value="{{$channel->id}}">{{ucfirst($channel->name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- {{$channels[$session]}} --}}
                                    </div>  
                                    <br>
                                    <div class="row" id="feeToAssign"></div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger btn-sm">@lang('Submit')</button>
                                    </div> 
                                </form>
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
    </div>
@endsection

@section('jsFiles')
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(document).ready(function(){
            $('#channel').on('change', function(){
            if($(this).val() != 0){
                $.ajax({
                url: '{{url("/fees/assignListAction")}}',
                type: "GET",
                data: {
                    "_token": "{{ csrf_token() }}",
                    channel_id: $(this).val(),
                    session: $('#year').val(),
                },
                success: function(data){
                        $('#feeToAssign').html(data);
                }
            });
            }  
        });
        });
    </script>
@endsection
  