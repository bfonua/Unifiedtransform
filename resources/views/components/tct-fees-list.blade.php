@component('components.fee-type-form', [
    'buttonTitle' => 'Add Fee',
    'modal_name' => 'myModal',
    'title' => 'New Fee',
    'put_method' => '',
    'url' => url('fees/tct_create'),
])
    @slot('buttonType')
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal"><i class="material-icons">add</i> 
    @endslot
    @slot('form_content')
        {{-- <div class="form-group">
            <label for="name" class="col-sm-4 control-label">@lang('Name')</label>
            <div class="col-sm-8">
            <input type="text" class="form-control" id="name" name="name">
            </div>
        </div> --}}
        <div class="form-group">
            <label for="session" class="col-sm-4 control-label">@lang('Session')</label>
            <div class="col-sm-8">
            <input type="text" class="form-control" id="session" name="session" value="{{now()->year}}">
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-4 control-label">@lang('Fee Type')</label>
            <div class="col-sm-8">
                <select id="type" class="form-control" name="type">
                    @foreach ($feeTypes as $type)
                        <option value="{{$type->id}}">{{ucfirst($type->name)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="channel" class="col-sm-4 control-label">@lang('Fee Channel (# of fees)')</label>
            <div class="col-sm-8">
                <select id="channel" class="form-control" name="channel">
                    @foreach ($feeChannels as $channel)
                        <option value="{{$channel->id}}">{{ucfirst($channel->name)." (".$channel->fees->count().")"}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="active" class="col-sm-4 control-label">@lang('Active')</label>
            <div class="col-sm-8">
                <select id="active" class="form-control" name="active">
                    <option value="1">Active</option>
                    <option value="0">Not Active</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="amount" class="col-sm-4 control-label">@lang('Amount')</label>
            <div class="col-sm-8">
            <input type="text" class="form-control" id="amount" name="amount" placeholder="0.00">
            </div>
        </div>
    @endslot
@endcomponent

@if((Auth::user()->role == 'admin' || Auth::user()->role == 'master') && $fees->first() && $fees->first()->session < now()->year)
    @component('components.fee-type-form', [
        'buttonTitle' => 'Update Session',
        'modal_name' => 'feeModal',
        'title' => 'Update Fee Session',
        'put_method' => '',
        'url' => url('fees/tct_update_session'),
    ])
        @slot('buttonType')
            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#feeModal"><i class="material-icons">warning</i>
        @endslot
        @slot('form_content')
            <div class="form-group">
                <label for="session" class="col-sm-4 control-label">@lang('Session')</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" id="session" name="session" value="{{ now()->year }}">
                </div>
            </div>
        @endslot
    @endcomponent
@endif
<hr>
          
<!-- Removed table-data-div to resolve form alignment issues -->
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Channel</th>
                @foreach ($feeTypes as $feeType)
                    <th class="text-center">{{$feeType->name}}</th>
                @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach ($feeChannels as $feeChannel)
                <tr>
                    <td class="text-center">{{$loop->index + 1}}</td>
                    <td class="text-center">{{$feeChannel->name}}</td>
                    @foreach ($feeChannel->fees as $feeChannelFee)
                        <td class="text-center">
                            {{$feeChannelFee->amount}} 
                            @component('components.fee-type-form', [
                                'buttonTitle' => '',
                                'modal_name' => 'myModal'.$feeChannelFee->id,
                                'title' => 'Edit Fee',
                                'put_method' => method_field('PUT'),
                                'url' => url('fees/tct_create/'.$feeChannelFee->id),
                            ])
                                @slot('buttonType')
                                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#myModal{{$feeChannelFee->id}}"><i class="material-icons">edit</i> 
                                @endslot
                                @slot('form_content')
                                    <div class="row form-group">
                                        <label for="session" class="col-sm-4 control-label">@lang('Session')</label>
                                        <div class="col-sm-8">
                                        <input type="text" class="form-control" id="session" name="session" value="{{$feeChannelFee->session}}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="type" class="col-sm-4 control-label">@lang('Fee Type')</label>
                                        <div class="col-sm-8">
                                            <select id="type" class="form-control" name="type">
                                                @foreach ($feeTypes as $type)
                                                    <option value="{{$type->id}}" {{($type->id == $feeChannelFee->fee_type_id)?"selected='selected'":''}}>{{ucfirst($type->name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="channel" class="col-sm-4 control-label">@lang('Fee Channel')</label>
                                        <div class="col-sm-8">
                                            <select id="channel" class="form-control" name="channel">
                                                @foreach ($feeChannels as $channel)
                                                    <option value="{{$channel->id}}" {{($channel->id == $feeChannelFee->fee_channel_id)?"selected='selected'":''}}>{{ucfirst($channel->name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="active" class="col-sm-4 control-label">@lang('Active')</label>
                                        <div class="col-sm-8">
                                            <select id="active" class="form-control" name="active">
                                                <option value="1" {{($feeChannelFee->active == 1)?'selected="selected"':''}}>Active</option>
                                                <option value="0" {{($feeChannelFee->active == 0)?'selected="selected"':''}}>Not Active</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="amount" class="col-sm-4 control-label">@lang('Amount')</label>
                                        <div class="col-sm-8">
                                        <input type="text" class="form-control" id="amount" name="amount" value="{{$feeChannelFee->amount}}">
                                        </div>
                                    </div>
                                @endslot
                            @endcomponent
                        </td>
                    @endforeach
                </tr>
            @endforeach
          </tbody>
    </table> 

</div>
  