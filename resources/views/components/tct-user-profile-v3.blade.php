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
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade in">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> {{session('success')}}
    </div>
@endif

<div>
    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <div class="col-md-2" text-center>
            <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
            <hr>
            <!-- INACTIVE / REINSTATE BUTTONS -->
            <div class="row text-center">
                @if ($user->active)
                    @include('layouts.master.set-inactive')
                @else
                    @if($hasReinstate)
                        @if($reinstateRequest->approved)
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
            <br>
            <div class="text-center">
                <a role="button" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will delete the student and ALL related records')" href="{{url('tct_delete_student/'.$user->id)}}"><i class="material-icons">delete</i> @lang('Delete Record')</a>
            </div>
        </div>
    @elseif(Auth::user()->role == 'teacher')
        <div class="col-md-2" text-center>
            <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
            <hr>
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
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#finance">Finance</a>
                </li>
                @if($user->studentInfo->section->class->options and $user->studentInfo->section->class->optionCount > 0)
                <li class="nav-item">
                    <a class="nav-link active " data-toggle="tab" href="#subject">Electives</a>
                </li>
                @endif
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
                                @if(!$user->active && $inactiveRequest)
                                    <tr>
                                        <td colspan="4" class="bg-info text-white text-center"><b>Inactive details</b></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Type'):</td>
                                        <td>{{ucfirst($inactiveRequest->type)}}</td>
                                        <td>@lang('Inactive Date')</td>
                                        <td>{{Carbon\Carbon::parse($inactiveRequest->created_at)->format('d/m/Y')}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Inactive Notes'):</td>
                                        <td colspan="3">{{$inactiveRequest->notes}}</td>
                                    </tr>
                                    @if($hasReinstate && $reinstateRequest)
                                        <tr>
                                            <td colspan="4">
                                                Reinstated on {{Carbon\Carbon::parse($reinstateRequest->created_at)->format('d/m/Y')}}
                                                - {{$reinstateRequest->notes}}
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
                                    <td colspan="6" class="bg-dark text-white text-center">Enrollment History</td>
                                </tr>
                                <thead>
                                    <tr class="bg-secondary text-white">
                                        <th class="text-center">Session</th>
                                        <th class="text-center">Form</th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">House</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Channel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($enrollmentHistory) && count($enrollmentHistory) > 0)
                                        @foreach($enrollmentHistory as $index => $record)
                                            @php
                                                $isCurrent = isset($record->type) && $record->type === 'enrollment' && $record->session == $user->studentInfo->session;
                                                $isUnavailable = isset($record->type) && $record->type === 'unavailable';
                                                $isInactive = isset($record->type) && $record->type === 'inactive';
                                                $isReinstate = isset($record->type) && $record->type === 'reinstate';
                                            @endphp
                                            
                                            @if($isInactive)
                                                {{-- Inactive Event Row --}}
                                                <tr class="text-dark">
                                                    <td class="text-center"><strong>{{$record->session}}</strong></td>
                                                    <td colspan="5" class="text-center">
                                                        <strong>{{$record->inactive_type}}</strong> - 
                                                        {{Carbon\Carbon::parse($record->inactive_date)->format('d/m/Y')}}
                                                        @if($record->inactive_notes)
                                                            <br><small class="text-muted">{{$record->inactive_notes}}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif($isReinstate)
                                                {{-- Reinstate Event Row --}}
                                                <tr class="text-dark">
                                                    <td class="text-center"><strong>{{$record->session}}</strong></td>
                                                    <td colspan="5" class="text-center">
                                                        <strong>REINSTATED</strong> - 
                                                        {{Carbon\Carbon::parse($record->reinstate_date)->format('d/m/Y')}}
                                                        @if($record->reinstate_notes)
                                                            <br><small class="text-muted">{{$record->reinstate_notes}}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @else
                                                {{-- Regular Enrollment Row --}}
                                                <tr class="{{ $isCurrent ? 'bg-light' : ($isUnavailable ? 'text-muted' : '') }}">
                                                    <td class="text-center">
                                                        @if($isCurrent)<strong>@endif
                                                        {{$record->session}}
                                                        @if($isCurrent)</strong>@endif
                                                    </td>
                                                    @if($isUnavailable)
                                                        <td colspan="5" class="text-center">
                                                            Not Available
                                                        </td>
                                                    @else
                                                        <td class="text-center">
                                                            @if($isCurrent)<strong>@endif
                                                            {{$record->form_name}}
                                                            @if($isCurrent)</strong>@endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isCurrent)<strong>@endif
                                                            {{$record->form_num}}
                                                            @if($isCurrent)</strong>@endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isCurrent)<strong>@endif
                                                            {{$record->house_name}}
                                                            @if($isCurrent)</strong>@endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isCurrent)<strong>@endif
                                                            {{$record->status}}
                                                            @if($isCurrent)</strong>@endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isCurrent)<strong>@endif
                                                            {{$record->channel_name}}
                                                            @if($isCurrent)</strong>@endif
                                                        </td>
                                                    @endif
                                                </tr>
                                                @if(isset($record->notes) && $record->notes && !$isUnavailable)
                                                    <tr class="{{ $isCurrent ? 'bg-light' : '' }}">
                                                        <td colspan="6" class="text-muted"><small><strong>Notes:</strong> {{$record->notes}}</small></td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No enrollment history available.</td>
                                        </tr>
                                    @endif
                                </tbody>
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
                                                <h4>{{$session}} <small> - Channel: {{ $feeChannels[$session] ?? '' }}</small></h4>
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
                                                        @php 
                                                        $total = ['assign' => 0,'pay' => 0,'remain' => 0];
                                                        $schoolType = ['Term 1', 'Term 2', 'Term 3', 'Term 4'];
                                                        $typeIDs = isset($schoolFeeData[$session]) ? $schoolFeeData[$session]['typeIDs'] : [];
                                                        $schoolAssign = isset($schoolFeeData[$session]) ? $schoolFeeData[$session]['schoolAssign'] : 0;
                                                        $schoolFees = ['School Fees','term1', 'term2', 'term3', 'term4'];
                                                        $schoolAmountPaid = isset($oldPayments[$session]) ? $oldPayments[$session]->whereIn('fee_type', $schoolFees)->sum('amount') : 0;
                                                        $schoolFeesAccrue = 0;
                                                        @endphp
                                                        @foreach ($feeList[$session]['fee_id'] as $id)
                                                            @php
                                                                $currentFee = $feesWithChannels[$id] ?? null;
                                                            @endphp
                                                            @if($currentFee && isset($currentFee->fee_type) && $currentFee->fee_type)
                                                            @php
                                                                $type = $currentFee->fee_type->name;
                                                                $assign = $currentFee->amount;
                                                            @endphp
                                                            <tr>
                                                                <th scope="row" class="text-center">{{$type}}</th>
                                                                <td class="text-center">{{$userSer->numberformat($assign)}}</td>
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
                                                                        @php 
                                                                        // Use pre-loaded old payment amounts for pre-2020
                                                                        $payment = $oldPaymentAmounts[$session][$type] ?? 0;
                                                                        @endphp
                                                                        <td class="text-center">{{$userSer->numberformat($payment)}}</td>
                                                                    @endif
                                                                @else
                                                                    @php 
                                                                    // Use pre-loaded payments
                                                                    $paymentKey = $id . '_' . $session;
                                                                    $payment = isset($allPayments[$paymentKey]) ? $allPayments[$paymentKey]->sum('amount') : 0;
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
                                                            @endif
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
                                                                @if (Auth::user()->role != 'teacher')
                                                                    <div class="text-center">
                                                                        <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                                                            {{csrf_field()}}
                                                                            <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                            <input type="hidden" value="{{$session}}" name="session">
                                                                            <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Reassign</button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            {{-- PAYMENT BUTTON --}}
                                                            <td colspan="2">
                                                                @if (Auth::user()->role != 'teacher')
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
                                                                                            $payValue = $feeChannels[$session] ?? 'No Channel Assigned';
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
                                                                                @php
                                                                                    $feeListIDs = collect($feeList[$session]['fee_id'])->map(function($id) use ($feesWithChannels) { return $feesWithChannels[$id] ?? null; })->filter();
                                                                                    // Log::info($feeListIDs);
                                                                                @endphp
                                                                                @foreach ($feeListIDs as $feeListID)
                                                                                    @php
                                                                                    $id = $feeListID->id;
                                                                                    $paymentKey = $id . '_' . $session;
                                                                                    $paymentsMade = $allPayments[$paymentKey] ?? collect([]);
                                                                                    // Log::info($paymentsMade);
                                                                                    if($paymentsMade->count() > 0){
                                                                                        $text = 1;
                                                                                        $assignAm = $feeListID->amount;
                                                                                        $paymentAm = $paymentsMade->sum('amount');
                                                                                        $remainAm = $assignAm - $paymentAm;
                                                                                    } else{ 
                                                                                        $text = 0;
                                                                                        $remainAm = $feeListID->amount;
                                                                                    }
                                                                                    @endphp
                                                                                    <div class="row form-group">
                                                                                        <label for="type" class="col-sm-3 control-label">Fee Type</label>
                                                                                        <div class="col-sm-4">
                                                                                            <input id = "type" name="typePaid" class="form-control" value="{{$feeListID->fee_type->name}}" readonly>
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
                                                                                        @php $payValue = $feeChannels[$session] ?? 'No Channel Assigned'; @endphp
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
                                                                                        $currentFee = $feesWithChannels[$id] ?? null;
                                                                                    @endphp
                                                                                    @if($currentFee && isset($currentFee->fee_type) && $currentFee->fee_type)
                                                                                    @php
                                                                                        $type = $currentFee->fee_type->name;
                                                                                        if(in_array($session, [2018, 2019]) and in_array($type, ['Term 1', 'Term 2', 'Term 3', 'Term 4'])){
                                                                                            if(!$schoolFeesDone){
                                                                                                $assignAm = $schoolFeeData[$session]['schoolAssign'] ?? 0;
                                                                                                $schoolFees = ['School Fees','term1', 'term2', 'term3', 'term4'];
                                                                                                $paymentAm = isset($oldPayments[$session]) ? $oldPayments[$session]->whereIn('fee_type', $schoolFees)->sum('amount') : 0;
                                                                                                $schoolFeesDone = 1;
                                                                                                $type = "School Fees";
                                                                                            }
                                                                                        } else{
                                                                                            $assignAm = $currentFee->amount;
                                                                                            // Use pre-loaded old payment amounts
                                                                                            $paymentAm = $oldPaymentAmounts[$session][$type] ?? 0;
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
                                                                                    @endif        
                                                                                @endforeach
                                                                            @endslot
                                                                        @endcomponent
                                                                    </div>
                                                                @endif
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
                                                                @if (Auth::user()->role != 'teacher')
                                                                    <div class="text-center">
                                                                        <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                                                            {{csrf_field()}}
                                                                            <input type="hidden" value="{{$user->id}}" name="user_id">
                                                                            <input type="hidden" value="{{$session}}" name="session">
                                                                            <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Assign Fees</button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <div class="text-center text-muted">
                                                                        Not Available
                                                                    </div>
                                                                @endif
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
                                        @if (Auth::user()->role != 'teacher')
                                            <form class="form-horizontal" action="{{url('fees/reassignForm')}}" method="post">
                                                {{csrf_field()}}
                                                <input type="hidden" value="{{$user->id}}" name="user_id">
                                                <input type="hidden" value="{{now()->year}}" name="session">
                                                <button type="submit" class="btn btn-primary btn-sm data-to"><i class="material-icons">assignment_returned</i> Assign Fees</button>
                                            </form>
                                        @else
                                            <div class="text-muted">Fee assignment not available.</div>
                                        @endif
                                    @else
                                        Please register / promote student inorder to Assign Fees!
                                    @endif
                                @endif     
                            </div>
                            <div class="col-xs-6 container">
                                @php 
                                    $allPay = $allPayForDisplay;
                                    $count = 1;
                                @endphp
                                {{-- {{$oldPay}} --}}
                                @if($allPay->first())
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
                                                                        {{-- <input id = "type" name="type" class="form-control" value="{{$pay->fees->fee_type->name}}" readonly> --}}
                                                                        <select id="type" class="form-control" name="type">   
                                                                            @php
                                                                                $feeIDs = $feeList[$pay->session]['fee_id'];
                                                                            @endphp
                                                                            @foreach ($feeIDs as $feeID)
                                                                                @if(isset($feesWithChannels[$feeID]))
                                                                                    @php
                                                                                        $fee = $feesWithChannels[$feeID];
                                                                                    @endphp
                                                                                    <option value="{{$fee->fee_type->id}}">{{$fee->fee_type->name}}</option>
                                                                                @endif
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
                                                                        @php $feeType = $oldPaymentFeeTypes[$oldPay->year] ?? [];  @endphp
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Electives Details -->
                @if($user->studentInfo->section->class->options and $user->studentInfo->section->class->optionCount > 0)
                <div class="tab-pane" id="subject">
                    <br/>
                    <div class="row col-md-12">
                        <table class="table">
                            <tr>
                                <td  class="bg-dark text-white text-center">Electives & Grades</td>
                            </tr>
                        </table>
                        <div class="col-xs-6 container">
                            <div class="text-center">
                                @foreach($subjectList as $session => $chosenOptions)
                                    @if($session > 2020)
                                    <h4>{{$session}}<small> - Electives</small></h4>
                                    <table class="table">
                                        <thead>
                                            <tr class="bg-secondary text-white">
                                                <th scope="col" class="text-center">Option</th>
                                                <th scope="col" class="text-center">Course</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($session > $user->studentInfo->session)
                                                <tr>
                                                    <td colspan="2" class="text-center">
                                                        Please promote student to assign subjects for the school year   
                                                    </td>
                                                </tr>
                                            @elseif($chosenOptions != [])
                                                @foreach(range(0,$user->studentInfo->section->class->optionCount-1) as $i)
                                                    <tr>
                                                        <td class="text-center">Option {{$i+1}}</td>
                                                        <td class="text-center"> 
                                                            @php
                                                                $subjectAssignment = $subID->where('session', $session)->where('option', $i+1)->first();
                                                            @endphp
                                                            {{ (in_array($i+1, $chosenOptions) && $subjectAssignment)? $subjectAssignment->subject->name : "-" }}
                                                        </td>
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
                                                                @foreach(range(0,$user->studentInfo->section->class->optionCount-1) as $i)
                                                                    <div class="row form-group">
                                                                        <label for="type" class="col-sm-3 control-label">Option {{$i+1}}</label>
                                                                        <div class="col-sm-5">
                                                                            <select id="option{{$i+1}}" class="form-control" name="option{{$i+1}}">
                                                                                <option value="">N/A</option>
                                                                                @foreach ($optionSubs as $sub)
                                                                                    <option value="{{$sub}}"
                                                                                        @php
                                                                                            $selectedOption = $subID->where('session', $session)->where('option', $i+1)->where('subject_id', $sub);
                                                                                        @endphp
                                                                                        @if($selectedOption->first())
                                                                                            selected = "selected"
                                                                                        @endif
                                                                                        >
                                                                                        {{$allSubjects[$sub]->name}}
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
                                                                        @foreach(range(0,$user->studentInfo->section->class->optionCount-1) as $i)
                                                                            <div class="row form-group">
                                                                                <label for="type" class="col-sm-3 control-label">Option {{$i+1}}</label>
                                                                                <div class="col-sm-5">
                                                                                    <select id="option{{$i+1}}" class="form-control" name="option{{$i+1}}">
                                                                                        <option value="">N/A</option>
                                                                                        @foreach ($allSubjects as $sub)
                                                                                            <option value="{{$sub->id}}">{{$sub->name}}</option>
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
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('jsFiles')
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

