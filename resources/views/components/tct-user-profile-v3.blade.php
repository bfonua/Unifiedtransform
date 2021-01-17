<!-- Include Service into Blade file -->

@php $userSer = $user; @endphp
@inject('userSer', 'App\Services\User\UserService')

@if($errors->any())
@foreach ($errors->all() as $error)
    <div class="bg-danger text-white">{{$error}}</div>
@endforeach
<br>
@endif
@if(isset($error2))
    <div class="bg-danger text-white">{{$error}}</div>
@endif

<div>
    @if(Auth::user()->role == 'admin')
        <div class="col-md-2" text-center>
            <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
            <hr>
            <!-- INACTIVE / REINSTATE BUTTONS -->
            <div class="row text-center">
                @if ($user->active)
                    @include('layouts.master.set-inactive')
                @else
                    @if($userSer->checkReinstate($user))
                        @if($userSer->getReinstateRequest($user)->approved)
                            @include('layouts.master.set-inactive')
                        @else
                            @include('layouts.master.reinstate-approval')
                        @endif
                    @else
                        @include('layouts.master.reinstate-form')
                    @endif
                @endif

            </div>
            <br>
            <!-- PROMOTE BUTTON -->
            <div class="row text-center">
                @if($user->active & $user->studentInfo->session != date('Y'))
                    @include('layouts.master.promote-tct-student')
                    <br>
                @endif
            </div>
            <br>
            <!-- EDIT BUTTONS -->
            <div class="row text-center">
                @include('layouts.master.edit-details-form')
            </div>
        </div>
    @endif
    <div class="col-md-10" id="main-container">
        <!-- STUDENT SUMMARY -->
        <div class="row">
            @component('components.tct-student-summary',['user'=>$user])
            @endcomponent 
        </div>
        <hr>
        <div class="row">
            <!-- NAV TABS -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active " data-toggle="tab" href="#general">Administration</a>
                </li>
                @if(Auth::user()->role != 'teacher')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#finance">Finance</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link active " data-toggle="tab" href="#subject">Subjects</a>
                </li>
            </ul>
            <!-- NAV TABS CONTENT -->
            <div class="tab-content">
                <!-- Admin Details-->
                <div class="tab-pane active" id="general">
                    <br/>
                    <div class="row">
                        <div class="container col-md-7">
                            <table class="table">
                                <tr>
                                    <td colspan="4" class="bg-dark text-white text-center">Administration Information</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('TCT ID'):</td>
                                    <td>{{$user->studentInfo['tct_id']}}</td>
                                    <td class="text-primary">@lang('Session'):</td>
                                    <td>{{$user->studentInfo['session']}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('Form'):</td>
                                    <td>{{$user->studentInfo->section->class->class_number}}{{$user->studentInfo->section->section_number}}</td>
                                    <td class="text-primary">@lang('Form #'):</td>
                                    <td>{{$user->studentInfo['form_num']}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('House'):</td>
                                    <td>{{$user->studentInfo->house->house_name}}</td>
                                    <td class="text-primary">@lang('Start Date')</td>
                                    <td>{{Carbon\Carbon::parse($user->studentInfo['created_at'])->format('d/m/Y')}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('Status'):</td>
                                    <td>{{ucfirst($user->studentInfo->group)}}</td>
                                    <td class="text-primary">@lang('Registration Date')</td>
                                    <td>{{Carbon\Carbon::parse($user->studentInfo['updated_at'])->format('d/m/Y')}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('Previous School'):</td>
                                    <td>{{$user->studentInfo->previous_school}}</td>
                                    <td class="text-primary">@lang('Previous Class')</td>
                                    <td>{{$user->studentInfo->previous_form}}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary">@lang('Notes'):</td>
                                    <td colspan="2">{{$user->studentInfo['reg_notes']}}</td>
                                </tr>
                                @if(!$user->active)
                                    <tr>
                                        <td colspan="4" class="bg-info text-white text-center"><b>Inactive details</b></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Type'):</td>
                                        <td>{{ucfirst($userSer->getInactiveRequest($user)->type)}}</td>
                                        <td>@lang('Inactive Date')</td>
                                        <td>{{Carbon\Carbon::parse($userSer->getInactiveRequest($user)->created_at)->format('d/m/Y')}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Inactive Notes'):</td>
                                        <td colspan="3">{{$userSer->getInactiveRequest($user)->notes}}</td>
                                    </tr>
                                    @if($userSer->checkReinstate($user))
                                        <tr>
                                            <td colspan="4">
                                                Reinstated on {{Carbon\Carbon::parse($userSer->getReinstateRequest($user)->created_at)->format('d/m/Y')}}
                                                - {{$userSer->getReinstateRequest($user)->notes}}
                                            </td>
                                        </tr>
                                    @endif
                                    @endif
                                <tr>
                                    <td colspan="4" class="bg-dark text-white text-center">Personal details</td>
                                </tr>
                                <tr>
                                    <td>@lang('Last Name'):</td>
                                    <td>{{$user->lst_name}}</td>
                                    <td>@lang('Given Names')</td>
                                    <td>{{$user->given_name}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Date of Birth'):</td>
                                    <td>{{Carbon\Carbon::parse($user->studentInfo->birthday)->format('d/m/Y')}}</td>
                                    <td>@lang('Category'):</td>
                                    <td>{{$user->studentInfo['category_id']}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Nationality'):</td>
                                    <td>{{$user->nationality}}</td>
                                    <td>@lang('Village'):</td>
                                    <td>{{$user->village}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Church'):</td>
                                    <td colspan="2">{{$user->studentInfo['church']}}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="bg-dark text-white text-center">Health and Contact Details</td>
                                </tr>
                                <tr>
                                    <td>@lang('Health Conditions'):</td>
                                    <td>{{$user->health_conditions}}</td>
                                    <td>@lang('Blood Type'):</td>
                                    <td>{{$user->blood_group}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Father\'s Name'):</td>
                                    <td>{{$user->studentInfo['father_name']}}</td>
                                    <td>@lang('Mother\'s Name'):</td>
                                    <td>{{$user->studentInfo['mother_name']}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Father\'s Phone Number'):</td>
                                    <td>{{$user->studentInfo['father_phone_number']}}</td>
                                    <td>@lang('Mother\'s Phone Number'):</td>
                                    <td>{{$user->studentInfo['mother_phone_number']}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Father\'s Occupation'):</td>
                                    <td>{{$user->studentInfo['father_occupation']}}</td>
                                    <td>@lang('Mother\'s Occupation'):</td>
                                    <td>{{$user->studentInfo['mother_occupation']}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="container col-md-5">
                            <table class="table">
                                <tr>
                                    <td colspan="4" class="bg-dark text-white text-center">Enrollment History</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Finance Details -->
                <div class="tab-pane" id="finance">
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tr>
                                    <td  class="bg-dark text-white text-center">Financial Information</td>
                                </tr>
                            </table>
                            <div class="col-xs-6 container">
                                @if($assigned > 0)
                                    @php                              
                                        $firstYear = "20".substr($user->studentInfo->tct_id,0,2);
                                        $years = range(now()->year, $firstYear);
                                    @endphp
                                    @foreach ($years as $session) 
                                        @if(isset($feeList[$session]))
                                            <div class="text-center">
                                                <h4>{{$session}}<small> - Channel: {{\App\Fee::find($feeList[$session]['fee_id'])->first()->fee_channel->name}}</small></h4>
                                            </div>
                                            <table class="table">
                                                @if(in_array($session, $sessions))
                                                    <thead>
                                                        <tr class="bg-secondary text-white">
                                                            <th scope="col" class="text-center">Session</th>
                                                            <th scope="col" class="text-center">Assigned</th>
                                                            <th scope="col" class="text-center">Paid</th>
                                                            <th scope="col" class="text-center">Remaining</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $total = ['assign' => 0,'pay' => 0,'remain' => 0]; 
                                                        if($session < "2020"){
                                                            $schoolType = ['Term 1', 'Term 2', 'Term 3', 'Term 4'];
                                                            $typeIDs = \App\FeeType::whereIn('name', $schoolType)->pluck('id')->toArray();
                                                            $allUserFees = \App\Assign::where('user_id', $user->id)
                                                                ->where('session', $session)
                                                                ->pluck('fee_id')->toArray();
                                                            $schoolAssign = \App\Fee::find($allUserFees)->whereIn('fee_type_id', $typeIDs)->sum('amount');
                                                            $schoolFees = ['School Fees','term1', 'term2', 'term3', 'term4'];
                                                            $schoolAmountPaid = \App\PaymentMigrate::where('tct_id', $user->studentInfo->tct_id)
                                                                ->where('year', $session)
                                                                ->whereIn('fee_type', $schoolFees)->sum('amount');
                                                            $schoolFeesAccrue = 0;
                                                        }                                                                                      
                                                        @endphp
                                                        {{-- Session is {{$schoolAmountPaid}} --}}
                                                        @foreach ($feeList[$session]['fee_id'] as $id)
                                                            <tr>
                                                                <th scope="row" class="text-center">{{$type = \App\Fee::find($id)->fee_type->name}}</th>
                                                                <td class="text-center">{{$userSer->numberformat($assign = \App\Fee::find($id)->amount)}}</td>
                                                                {{-- Checks if Session is before 2020 - to use old Payments table --}}
                                                                @if($session < "2020")
                                                                    {{-- Checks if fee is a school tpye --}}
                                                                    @if(in_array($type, $schoolType))
                                                                        @if($schoolAmountPaid - $schoolFeesAccrue >= $assign)
                                                                            <td class="text-center">
                                                                                {{$userSer->numberformat($payment = $assign)}}
                                                                            </td>
                                                                        @else
                                                                            <td class="text-center">
                                                                                {{$userSer->numberformat($payment = $schoolAmountPaid-$schoolFeesAccrue)}}
                                                                            </td>
                                                                        @endif
                                                                        @php $schoolFeesAccrue += $payment; @endphp
                                                                    @else
                                                                        @php $payment = $userSer->getPayment($user->id, $session, $id, 0, $type) @endphp
                                                                        <td class="text-center">{{$userSer->numberformat($payment)}}</td>
                                                                    @endif
                                                                @else
                                                                    @php $payment = $userSer->getPayment($user->id, $session, $id) @endphp
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
                                                            {{-- ASSIGN BUTTON --}}
                                                            <td colspan="2">
                                                                <div class="text-center">
                                                                    <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                                                        {{csrf_field()}}
                                                                        <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                        <input type="hidden" value="{{$session}}" name="session">
                                                                        <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Reassign</button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                            {{-- PAYMENT BUTTON --}}
                                                            <td colspan="2">
                                                                @if($session > 2019)
                                                                    <div class="text-center">
                                                                        @component('components.fee-type-form', [
                                                                            'buttonTitle' => 'Make Payment',
                                                                            'modal_name' => 'paymentModal'.$session,
                                                                            'title' => 'Set Payment '.$session,
                                                                            'put_method' => '',
                                                                            'url' => url('fees/tct_payment'),
                                                                        ])
                                                                            @slot('buttonType')
                                                                                <button type="button" class="btn btn-danger btn-sm {{($total['remain'] == 0)?'':''}}" data-toggle="modal" data-target="#paymentModal{{$session}}"><i class="material-icons">attach_money</i>  
                                                                            @endslot
                                                                            @slot('form_content')
                                                                                <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                                <input type="hidden" value="{{$session}}" name="session">
                                                                                <div class="row form-group">
                                                                                    <label for="channel" class="col-sm-3 control-label">@lang('Fee Channel')</label>
                                                                                    <div class="col-sm-5">
                                                                                        @php
                                                                                            $payValue = ($session == now()->year)? $user->studentInfo->channel->name: \App\Assign::where('session', $session)->where('user_id', $user->id)->first()->fees->fee_type->name;
                                                                                        @endphp
                                                                                        <input name="fee_channel" class="form-control" value="{{$payValue}}" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <label for="receipt" class="col-sm-3 control-label">@lang('Receipt #')</label>
                                                                                    <div class="col-sm-5">
                                                                                        <input id = "receipt" name="receipt" class="form-control">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <label for="payment_date" class="col-sm-3 control-label">@lang('Date')</label>
                                                                                    <div class="col-sm-4">
                                                                                        <input id = "payment_date" name="payment_date" class="form-control">
                                                                                    </div>
                                                                                </div>
                                                                                <hr>
                                                                                @foreach ($feeList[$session]['fee_id'] as $id)
                                                                                    @php
                                                                                    if($userSer->paymentExists($user->id, $id, $session)->first()){
                                                                                        $text = 1;
                                                                                        $assignAm = \App\Fee::find($id)->amount;
                                                                                        $paymentAm = $userSer->paymentExists($user->id, $id, $session)->sum('amount');
                                                                                        $remainAm = $assignAm - $paymentAm;
                                                                                    } else{ 
                                                                                        $text = 0;
                                                                                        $remainAm = \App\Fee::find($id)->amount;
                                                                                    }
                                                                                    @endphp
                                                                                    <div class="row form-group">
                                                                                        <label for="type" class="col-sm-3 control-label">Fee Type</label>
                                                                                        <div class="col-sm-4">
                                                                                            <input id = "type" name="typePaid" class="form-control" value="{{\App\Fee::find($id)->fee_type->name}}" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row form-group">
                                                                                        <label for="assigned" class="col-sm-3 control-label">{{($text)?'Remaining':'Assigned'}}</label>
                                                                                        <div class="col-sm-4">
                                                                                            <input id = "assigned" name="assigned" class="form-control" value="{{$userSer->numberformat($remainAm)}}" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    @if($remainAm > 0)
                                                                                        <div class="row form-group">
                                                                                            <label for="payment{{$id}}" class="col-sm-3 control-label">Payment</label>
                                                                                            <div class="col-sm-6">
                                                                                                <input id = "payment{{$id}}" name="payment[{{$id}}]" class="form-control" placeholder="0.00">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row form-group">
                                                                                            <label for="notes{{$id}}" class="col-sm-3 control-label">Notes</label>
                                                                                            <div class="col-sm-6">
                                                                                                <textarea id = "notes{{$id}}" name="notes[{{$id}}]" class="form-control"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    <hr>
                                                                                @endforeach
                                                                            @endslot
                                                                        @endcomponent
                                                                    </div>
                                                                @else
                                                                    <div class="text-center">
                                                                        @component('components.fee-type-form', [
                                                                            'buttonTitle' => 'Make Payment',
                                                                            'modal_name' => 'paymentOldModal'.$session,
                                                                            'title' => 'Make Old Payment '.$session,
                                                                            'put_method' => '',
                                                                            'url' => url('fees/tct_paymentMigrate'),
                                                                        ])
                                                                            @slot('buttonType')
                                                                                <button type="button" class="btn btn-danger btn-sm" {{($total['remain'] <= 0)?'disabled="disabled"':''}} data-toggle="modal" data-target="#paymentOldModal{{$session}}"><i class="material-icons">attach_money</i>  
                                                                            @endslot
                                                                            @slot('form_content')
                                                                                <input type="hidden" value="{{$user->studentInfo->tct_id}}" name="user_id">
                                                                                <input type="hidden" value="{{$session}}" name="session">
                                                                                <div class="row form-group">
                                                                                    <label for="channel" class="col-sm-3 control-label">@lang('Fee Channel')</label>
                                                                                    <div class="col-sm-5">
                                                                                        @php $payValue = ($session == now()->year)? $user->studentInfo->channel->name: \App\Assign::where('session', $session)->where('user_id', $user->id)->first()->fees->fee_channel->name; @endphp
                                                                                        <input name="fee_channel" class="form-control" value="{{$payValue}}" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <label for="receipt" class="col-sm-3 control-label">@lang('Receipt #')</label>
                                                                                    <div class="col-sm-5">
                                                                                        <input id = "receipt" name="receipt" class="form-control">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <label for="payment_date" class="col-sm-3 control-label">@lang('Date')</label>
                                                                                    <div class="col-sm-4">
                                                                                        <input id = "payment_date" name="payment_date" class="form-control">
                                                                                    </div>
                                                                                </div>
                                                                                <hr>
                                                                                @php
                                                                                    $assignFeeIDs = $feeList[$session]['fee_id'];
                                                                                    $schoolFeesDone = 0; // Switch for when school fees has been processed
                                                                                @endphp
                                                                                @foreach($assignFeeIDs as $id)
                                                                                    @php 
                                                                                        $type = \App\Fee::find($id)->fee_type->name;
                                                                                        // echo $type;
                                                                                        if(in_array($session, [2018, 2019]) and in_array($type, ['Term 1', 'Term 2', 'Term 3', 'Term 4'])){
                                                                                            if(!$schoolFeesDone){
                                                                                                $assignAm = $userSer->getSchoolassigned($user->id, $session);
                                                                                                $paymentAm = \App\PaymentMigrate::where('tct_id', $user->studentInfo->tct_id)
                                                                                                    ->where('year', $session)
                                                                                                    ->whereIn('fee_type', ['School Fees','term1', 'term2', 'term3', 'term4'])
                                                                                                    ->sum('amount');
                                                                                                $schoolFeesDone = 1;
                                                                                                $type = "School Fees";
                                                                                            }
                                                                                        } else{
                                                                                            $assignAm = \App\Fee::find($id)->amount;
                                                                                            $paymentAm = $userSer->getPayment($user->id, $session, $id, 0, $type);
                                                                                            // echo($type." ".$assignAm." ".$paymentAm);
                                                                                        }
                                                                                        $remainAm = $assignAm - $paymentAm
                                                                                    @endphp
                                                                                    @if($remainAm > 0)
                                                                                        <div class="row form-group">
                                                                                            <label for="type" class="col-sm-3 control-label">Fee Type</label>
                                                                                            <div class="col-sm-4">
                                                                                                <input id = "type" name="typePaid" class="form-control" value="{{$type}}" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row form-group">
                                                                                            <label for="assigned" class="col-sm-3 control-label">Remaining</label>
                                                                                            <div class="col-sm-4">
                                                                                                <input id = "assigned" name="assigned" class="form-control" value="{{$remainAm}}" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row form-group">
                                                                                            <label for="payment{{$id}}" class="col-sm-3 control-label">Payment</label>
                                                                                            <div class="col-sm-6">
                                                                                                <input id = "payment{{$id}}" name="payment[{{$id}}]" class="form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row form-group">
                                                                                            <label for="notes{{$id}}" class="col-sm-3 control-label">Notes</label>
                                                                                            <div class="col-sm-6">
                                                                                                <textarea id = "notes{{$id}}" name="notes[{{$id}}]" class="form-control"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif        
                                                                                @endforeach
                                                                            @endslot
                                                                        @endcomponent
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4"></td>
                                                        </tr>
                                                    </tbody>
                                                @endif
                                            </table>
                                        @else
                                            <div class="text-center">
                                                <h4>{{$session}}</h4>
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
                                                    @if($session == now()->year and $session != $user->studentInfo->session)
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                Please promote student to assign fees for the school year   
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <th class="text-center" colspan="2">Currently Not Assigned</th>
                                                            <th class="text-center" colspan="2">
                                                                <div class="text-center">
                                                                    <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                                                        {{csrf_field()}}
                                                                        <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                        <input type="hidden" value="{{$session}}" name="session">
                                                                        <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Assign Fees</button>
                                                                    </form>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table> 
                                        @endif
                                        <hr>
                                    @endforeach
                                @else
                                    Student has not been assigned! <br>
                                    <br>
                                    @if($user->studentInfo->session == now()->year)
                                    <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                        {{csrf_field()}}
                                        {{-- {{$session}} --}}
                                        <input type="hidden" value="{{$user->id}}" name="user_id">
                                        <input type="hidden" value="{{now()->year}}" name="session">
                                        <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Assign Fees</button>
                                    </form>
                                    @else
                                        Please register / promote student inorder to Assign Fees!
                                    @endif
                                @endif     
                            </div>
                            <div class="col-xs-6 container">
                                @php 
                                    $allPay = \App\Payment::where('user_id', $user->id)->orderBy('pay_date', 'desc')->get();
                                    $oldPayments = \App\PaymentMigrate::where('tct_id', $user->studentInfo->tct_id)->orderBy('pay_date', 'desc')->get();
                                    $count = 1;
                                @endphp
                                {{-- {{$oldPay}} --}}
                                <table class="table">
                                    <thead>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Receipt</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Session</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Edit</th>
                                    </thead>
                                    <tbody>

                                    {{-- CURRENT SESSIONS --}}
                                    @foreach($allPay as $pay)
                                        <tr>
                                            <td class="text-center">{{$loop->iteration}}</td>
                                            <td class="text-center">{{$pay->receipt}}</td>
                                            <td class="text-center">{{$pay->fees->fee_type->name}}</td>
                                            <td class="text-right">{{$pay->amount}}</td>
                                            <td class="text-center">{{$pay->session}}</td>
                                            <td class="text-center">{{$pay->pay_date}}</td>
                                            <td class="text-center">
                                                <div class="text-center">
                                                    @component('components.fee-type-form', [
                                                        'buttonTitle' => '',
                                                        'modal_name' => 'payment'.$pay->id,
                                                        'title' => 'Edit',
                                                        'put_method' => method_field('PUT'),
                                                        'url' => url('fees/tct_payment/'.$pay->id),
                                                    ])
                                                        @slot('buttonType')
                                                            <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#payment{{$pay->id}}"><i class="material-icons">edit</i>  
                                                        @endslot
                                                        @slot('form_content')
                                                            <input type="hidden" value="{{$user->id}}" name="user_id">
                                                            <input type="hidden" value="{{$user->studentInfo->channel_id}}" name="channel_id">
                                                            <div class="row form-group">
                                                                <label for="receipt" class="col-sm-3 control-label">@lang('Receipt #')</label>
                                                                <div class="col-sm-5">
                                                                    <input id = "receipt" name="receipt" class="form-control" value="{{$pay->receipt}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="payment_date" class="col-sm-3 control-label">@lang('Date')</label>
                                                                <div class="col-sm-4">
                                                                    <input id = "payment_date" name="payment_date" class="form-control"  value="{{$pay->pay_date}}">
                                                                </div>
                                                            </div>
                                                                <div class="row form-group">
                                                                <label for="type" class="col-sm-3 control-label">Fee Type</label>
                                                                <div class="col-sm-4">
                                                                    @php $feeIDs = $feeList[$pay->session]['fee_id']; @endphp
                                                                    {{-- <input id = "type" name="type" class="form-control" value="{{$pay->fees->fee_type->name}}" readonly> --}}
                                                                    <select id="type" class="form-control" name="type">   
                                                                        @foreach ($feeIDs as $feeID)
                                                                            <option value="{{\App\Fee::find($feeID)->fee_type->id}}">{{\App\Fee::find($feeID)->fee_type->name}}</option>
                                                                         @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="session" class="col-sm-3 control-label">Session</label>
                                                                <div class="col-sm-4">
                                                                    <input id = "session" name="session" class="form-control" value="{{$pay->session}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="amount" class="col-sm-3 control-label">Payment</label>
                                                                <div class="col-sm-6">
                                                                    <input id = "amount" name="amount" class="form-control" value="{{$pay->amount}}">
                                                                </div>
                                                            </div>
                                                        @endslot
                                                    @endcomponent
                                                </div>
                                            </td>
                                        </tr>
                                        @php $count++ @endphp
                                    @endforeach
                                    {{-- OLD PAY --}}
                                    @foreach($oldPayments as $oldPay)
                                        <tr>
                                            <td class="text-center">{{$count}}</td>
                                            <td class="text-center">{{$oldPay->receipt_num}}</td>
                                            <td class="text-center">{{$oldPay->fee_type}}</td>
                                            <td class="text-right">{{$oldPay->amount}}</td>
                                            <td class="text-center">{{$oldPay->year}}</td>
                                            <td class="text-center">{{$oldPay->pay_date}}</td>
                                            <td class="text-center">
                                                <div class="text-center">
                                                    @component('components.fee-type-form', [
                                                        'buttonTitle' => '',
                                                        'modal_name' => 'paymentMigrate'.$oldPay->pay_id,
                                                        'title' => 'Edit',
                                                        'put_method' => method_field('PUT'),
                                                        'url' => url('fees/tct_paymentMigrate/'.$oldPay->pay_id),
                                                    ])
                                                        @slot('buttonType')
                                                            <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#paymentMigrate{{$oldPay->pay_id}}"><i class="material-icons">edit</i>  
                                                        @endslot
                                                        @slot('form_content')
                                                            <input type="hidden" value="{{$oldPay->tct_id}}" name="user_id">
                                                            {{-- <input type="hidden" value="{{$user->studentInfo->channel_id}}" name="channel_id"> --}}
                                                            <div class="row form-group">
                                                                <label for="receipt" class="col-sm-3 control-label">@lang('Receipt #')</label>
                                                                <div class="col-sm-5">
                                                                    <input id = "receipt" name="receipt" class="form-control" value="{{$oldPay->receipt_num}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="payment_date" class="col-sm-3 control-label">@lang('Date')</label>
                                                                <div class="col-sm-4">
                                                                    <input id = "payment_date" name="payment_date" class="form-control"  value="{{$oldPay->pay_date}}">
                                                                </div>
                                                            </div>
                                                                <div class="row form-group">
                                                                <label for="type" class="col-sm-3 control-label">Fee Type</label>
                                                                <div class="col-sm-4">
                                                                    @php $feeType = \App\PaymentMigrate::where('year', $oldPay->year)->groupBy('fee_type')->pluck('fee_type')->toArray();  @endphp
                                                                    <select id="type" class="form-control" name="type">   
                                                                        @foreach ($feeType as $type)
                                                                            <option value="{{$type}}" {{($type==$oldPay->fee_type)?"selected = 'seclected'":""}}>{{$type}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="session" class="col-sm-3 control-label">Session</label>
                                                                <div class="col-sm-4">
                                                                    <input id = "session" name="session" class="form-control" value="{{$oldPay->year}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <label for="amount" class="col-sm-3 control-label">Payment</label>
                                                                <div class="col-sm-6">
                                                                    <input id = "amount" name="amount" class="form-control" value="{{$oldPay->amount}}">
                                                                </div>
                                                            </div>
                                                        @endslot
                                                    @endcomponent
                                                </div>
                                            </td>
                                        </tr>
                                        @php $count++ @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Subject Details -->
                <div class="tab-pane" id="subject">
                    <br/>
                    <div class="row col-md-12">
                        <table class="table">
                            <tr>
                                <td  class="bg-dark text-white text-center">Optional Courses</td>
                            </tr>
                        </table>
                        <div class="col-xs-6 container">
                            <div class="text-center">
                                @foreach($subjectList as $session => $chosenOptions)
                                    <h4>{{$session}}<small> - Subjects</small></h4>
                                    <table class="table">
                                        <thead>
                                            <tr class="bg-secondary text-white">
                                                <th scope="col" class="text-center">Option</th>
                                                <th scope="col" class="text-center">Course</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($session != now()->year and $session == $user->studentInfo->session)
                                                <tr>
                                                    <td colspan="2" class="text-center">
                                                        Please promote student to assign subjects for the school year   
                                                    </td>
                                                </tr>
                                            @elseif($chosenOptions != [])
                                                @foreach(range(0,4) as $i)
                                                    <tr>
                                                        <td class="text-center">Option {{$i+1}}</td>
                                                        <td class="text-center"> {{ (in_array($i+1, $chosenOptions))? \App\SubjectAssign::where([
                                                            'user_id'=> $user->id,
                                                            'session' => $session,
                                                            'option' => $i+1,
                                                        ])->first()->subject->name : "-" }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td class="text-center">
                                                        @component('components.fee-type-form', [
                                                            'buttonTitle' => 'Re-assign',
                                                            'modal_name' => 'reassignSubject',
                                                            'title' => 'Re-assign Optional Subjects',
                                                            'put_method' => '',
                                                            'url' => url("subject/reassign"),
                                                        ])
                                                            @slot('buttonType')
                                                                <button type="button" class="btn btn-warning btn-sm {{($session <= 2020)? 'disabled' : '' }}" data-toggle="modal" data-target="#reassignSubject"><i class="material-icons">edit</i>  
                                                            @endslot
                                                            @slot('form_content')
                                                                <input type="hidden" value="{{$session}}" name="session">
                                                                <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                @foreach(range(0, 4) as $i)
                                                                    <div class="row form-group">
                                                                        <label for="type" class="col-sm-3 control-label">Option {{$i+1}}</label>
                                                                        <div class="col-sm-5">
                                                                            <select id="option{{$i+1}}" class="form-control" name="option{{$i+1}}">
                                                                                <option value="">N/A</option>
                                                                                @foreach ($optionSubs as $sub)
                                                                                    <option value="{{$sub}}"
                                                                                        @php
                                                                                            $selectedOption = \App\SubjectAssign::where([
                                                                                                'user_id' => $user->id,
                                                                                                'option' => $i+1,
                                                                                                'subject_id' => $sub,
                                                                                            ])->get();
                                                                                        @endphp
                                                                                        @if($selectedOption->first())
                                                                                            selected = "selected"
                                                                                        @endif
                                                                                        >
                                                                                        {{\App\Subject::find($sub)->name}}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <br>
                                                                <div class="row form-group">
                                                                    <label for="session" class="col-sm-3 control-label">Session</label>
                                                                    <div class="col-sm-4">
                                                                        <input id = "session" name="session" class="form-control" value="{{now()->year}}">
                                                                    </div>
                                                                </div>
                                                            @endslot
                                                        @endcomponent
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="text-center">Currently Not Assigned</th>
                                                    <td class="text-center">
                                                        <div class="text-center">
                                                            {{-- SUBJECT ASSIGN FORM MODAL --}}
                                                            <div class="text-center">
                                                                @component('components.fee-type-form', [
                                                                    'buttonTitle' => 'Assign',
                                                                    'modal_name' => 'assignSubject',
                                                                    'title' => 'Assign Optional Subjects',
                                                                    'put_method' => '',
                                                                    'url' => url("subject/assign"),
                                                                ])
                                                                    @slot('buttonType')
                                                                        <button type="button" class="btn btn-primary btn-sm {{($session <= 2020)? 'disabled' : '' }}" data-toggle="modal" data-target="#assignSubject"><i class="material-icons">edit</i>  
                                                                    @endslot
                                                                    @slot('form_content')
                                                                        <input type="hidden" value="{{$session}}" name="session">
                                                                        <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                        @foreach(range(0, 4) as $i)
                                                                            <div class="row form-group">
                                                                                <label for="type" class="col-sm-3 control-label">Option {{$i+1}}</label>
                                                                                <div class="col-sm-5">
                                                                                    <select id="option{{$i+1}}" class="form-control" name="option{{$i+1}}">
                                                                                        <option value="">N/A</option>
                                                                                        @foreach ($optionSubs as $sub)
                                                                                            <option value="{{$sub}}">{{\App\Subject::find($sub)->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        <br>
                                                                        <div class="row form-group">
                                                                            <label for="session" class="col-sm-3 control-label">Session</label>
                                                                            <div class="col-sm-4">
                                                                                <input id = "session" name="session" class="form-control" value="{{now()->year}}">
                                                                            </div>
                                                                        </div>
                                                                    @endslot
                                                                @endcomponent
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@section('jsFiles')
    {{-- <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> --}}
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(function () {
            $('#inactive_date').datepicker({
                format: "yyyy-mm-dd",
            });
            $('#payment_date').datepicker({
                format: "yyyy-mm-dd",
                todayHighlight: true,
            });

            $('#birthday').datepicker({
                format: "yyyy-mm-dd",
            });
            $('#session').datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years"
            });
        });
        // $('#registerBtn').click(function () {
        //     $("#registerForm").submit();
        // });
    </script>

    <script>
        $("#btnPrint").on("click", function () {
        var tableContent = $('#profile-content').html();
        var printWindow = window.open('', '', 'height=720,width=1280');
        printWindow.document.write('<html><head>');
        printWindow.document.write('<link href="{{url('css/app.css')}}" rel="stylesheet">');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="container"><div class="col-md-12" id="academic-part">');
        printWindow.document.write(tableContent);
        printWindow.document.write('</div></div></body></html>');
        printWindow.document.close();
        // var academicPart = printWindow.document.getElementById("academic-part");
        // academicPart.appendChild(resultTable);
        printWindow.print();
        });
    </script>
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

