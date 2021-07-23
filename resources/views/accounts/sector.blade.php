@extends('layouts.app')
@section('title', __('Account Sectors'))
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="row">
                <div class="col-md-6">
                    <br>
                    <div class="panel panel-default">
                        <div class="page-panel-title">@lang('Account Sectors')</div>

                        <div class="panel-body">
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form class="form-horizontal" action="{{url('/accounts/create-sector')}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">@lang('Sector Code')</label>
                                <div class="col-md-4">
                                    <input id="name" type="text" class="form-control" name="code" value="{{ (!empty($sector->coder))?$sector->name:old('code') }}" placeholder="@lang('Sector Code')" required>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">@lang('Sector Name')</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ (!empty($sector->name))?$sector->name:old('name') }}" placeholder="@lang('Sector Name')" required>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                <label for="type" class="col-md-4 control-label">@lang('Sector Type')</label>

                                <div class="col-md-4">
                                    <select  class="form-control" name="type">
                                        <option value="income">@lang('Income')</option>
                                        <option value="expense">@lang('Expense')</option>
                                    </select>

                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" class="btn btn-danger">@lang(' Add Sector')</button>
                                </div>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <br>
                    <div style="width:100%;">
                        {{-- <canvas id="canvas"></canvas> --}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4>@lang('All Income Sectors')</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">@lang('Code')</th>
                                <th class="text-center">@lang('Sector Name')</th>
                                {{-- <th class="text-center">@lang('Type')</th> --}}
                                <th class="text-center">@lang('Active')</th>
                                <th class="text-center">@lang('Edit')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($sectors->where('type', 'income') as $sector)
                            <tr>
                                <td class="text-center">{{$sector->code}}</td>
                                <td>{{$sector->name}}</td>
                                {{-- <td class="text-center">{{ucfirst($sector->type)}}</td> --}}
                                <td class="text-center">
                                    @if($sector->active == 1)
                                        <i class="material-icons">done</i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- <a href="{{url('accounts/edit-sector/'.$sector->id)}}" class="btn btn-danger btn-xs" role="button">@lang('Edit')</a> --}}
                                    @component('components.fee-type-form', [
                                        'buttonTitle' => '',
                                        'modal_name' => 'sector'.$sector->id,
                                        'title' => 'Edit',
                                        'put_method' => method_field('PUT'),
                                        'url' => url('accounts/update-sector'),
                                    ])
                                        @slot('buttonType')
                                            <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#sector{{$sector->id}}"><i class="material-icons">edit</i>  
                                        @endslot
                                        @slot('form_content')
                                            <input type="hidden" value="{{$sector->id}}" name="sector_id">
                                            <div class="row form-group">
                                                <label for="code" class="col-sm-3 control-label">Sector Code</label>
                                                <div class="col-sm-4">
                                                    <input id = "code" name="code" class="form-control" value="{{$sector->code}}">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <label for="name" class="col-sm-3 control-label">Sector Name</label>
                                                <div class="col-sm-6">
                                                    <input id = "name" name="name" class="form-control" value="{{$sector->name}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sectorType{{$sector->id}}}" class="col-sm-3 control-label">Sector Type</label>
                                                <div class="col-sm-4">
                                                    <select id="type" id="sectorType{{$sector->id}}}" class="form-control" name="type">
                                                        <option value="income" {{($sector->type == "income")? 'selected="selected"' : ''}}>Income</option>
                                                        <option value="expense" {{($sector->type == "expense")? 'selected="selected"' : ''}}>Expense</option>                            
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sectorActive{{$sector->id}}}" class="col-sm-3 control-label">Sector Active</label>
                                                <div class="col-sm-4">
                                                    <select id="active" id="sectorActive{{$sector->id}}}" class="form-control" name="active">
                                                        <option value="1" {{($sector->active == "1")? 'selected="selected"' : ''}}>Active</option>
                                                        <option value="0" {{($sector->active == "0")? 'selected="selected"' : ''}}>Inactive</option>                            
                                                    </select>
                                                </div>
                                            </div>
                                        @endslot
                                    @endcomponent
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>@lang('All Expense Sectors')</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">@lang('Code')</th>
                                <th class="text-center">@lang('Sector Name')</th>
                                <th class="text-center">@lang('Active')</th>
                                {{-- <th class="text-center">@lang('Type')</th> --}}
                                <th class="text-center">@lang('Edit')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($sectors->where('type', 'expense') as $sector)
                            <tr>
                                <td class="text-center">{{$sector->code}}</td>
                                <td>{{$sector->name}}</td>
                                {{-- <td class="text-center">{{ucfirst($sector->type)}}</td> --}}
                                <td class="text-center">
                                    @if($sector->active == 1)
                                        <i class="material-icons">done</i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- <a href="{{url('accounts/edit-sector/'.$sector->id)}}" class="btn btn-danger btn-xs" role="button">@lang('Edit')</a> --}}
                                    @component('components.fee-type-form', [
                                        'buttonTitle' => '',
                                        'modal_name' => 'sector'.$sector->id,
                                        'title' => 'Edit',
                                        'put_method' => method_field('PUT'),
                                        'url' => url('accounts/update-sector'),
                                    ])
                                        @slot('buttonType')
                                            <button type="button" class="btn btn-xs" data-toggle="modal" data-target="#sector{{$sector->id}}"><i class="material-icons">edit</i>  
                                        @endslot
                                        @slot('form_content')
                                            <input type="hidden" value="{{$sector->id}}" name="sector_id">
                                            <div class="row form-group">
                                                <label for="code" class="col-sm-3 control-label">Sector Code</label>
                                                <div class="col-sm-4">
                                                    <input id = "code" name="code" class="form-control" value="{{$sector->code}}">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <label for="name" class="col-sm-3 control-label">Sector Name</label>
                                                <div class="col-sm-6">
                                                    <input id = "name" name="name" class="form-control" value="{{$sector->name}}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sectorType{{$sector->id}}}" class="col-sm-3 control-label">Sector Type</label>
                                                <div class="col-sm-4">
                                                    <select id="type" id="sectorType{{$sector->id}}}" class="form-control" name="type">
                                                        <option value="income" {{($sector->type == "income")? 'selected="selected"' : ''}}>Income</option>
                                                        <option value="expense" {{($sector->type == "expense")? 'selected="selected"' : ''}}>Expense</option>                            
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sectorActive{{$sector->id}}}" class="col-sm-3 control-label">Sector Active</label>
                                                <div class="col-sm-4">
                                                    <select id="active" id="sectorActive{{$sector->id}}}" class="form-control" name="active">
                                                        <option value="1" {{($sector->active == "1")? 'selected="selected"' : ''}}>Active</option>
                                                        <option value="0" {{($sector->active == "0")? 'selected="selected"' : ''}}>Inactive</option>                            
                                                    </select>
                                                </div>
                                            </div>
                                        @endslot
                                    @endcomponent
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> --}}
	{{-- <style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
    </style>
    <script>
        'use strict';

        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

		var color = Chart.helpers.color;
		var config = {
			type: 'bar',
			data: {
				datasets: [{
                    label: @json( __('Income')),
					backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
					borderColor: window.chartColors.green,
					fill: false,
					data: [@foreach($incomes as $s)
                        {
                            t:"{{Carbon\Carbon::parse($s->created_at)->format('Y-d-m')}}",
                            y:{{$s->amount}}
                        },
                        @endforeach]
                },{
                    label: @json( __('Expense')),
					backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
					borderColor: window.chartColors.red,
					fill: false,
					data: [@foreach($expenses as $s)
                        {
                            t:"{{Carbon\Carbon::parse($s->created_at)->format('Y-d-m')}}",
                            y:{{$s->amount}}
                        },
                        @endforeach]
                }]
            },
			options: {
				title: {
                    display: true,
					text: @json( __('Income and Expense (In Dollar) in Time Scale'))
				},
				scales: {
					xAxes: [{
						type: 'time',
						time: {
							parser: 'YYYY-DD-MM',
							tooltipFormat: 'll HH:mm'
						},
						scaleLabel: {
							display: true,
							labelString: @json( __('Date'))
						}
					}],
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: @json( __('Money'))
						}
					}]
				},
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);

		};
    </script> --}}
@endsection
