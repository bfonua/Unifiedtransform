@extends('layouts.app')
@section('title', __('Set Annual Budget'))
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" rel="stylesheet">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
          <br>
            <div class="row">
                <div class="col-md-6">
                <div class="panel panel-default">
                <div class="page-panel-title">@lang('Set Annual Budget')
                    {{-- <button class="btn btn-xs btn-success pull-right" role="button" id="btnPrint" ><i class="material-icons">print</i> @lang('Print This Expense List')</button></div> --}}
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="form-horizontal" action="{{url('/accounts/list-expense')}}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                            <label for="year" class="col-md-4 control-label">@lang('Year')</label>

                            <div class="col-md-6">
                                <input id="date" type="text" class="form-control datepicker" name="year" value="{{ old('year') }}" placeholder="@lang('Year')" required>

                                @if ($errors->has('year'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-danger">@lang('Get Budget')</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" action="#" method="post">
                    @php
                        $budgetCats = ['income', 'expense'];
                    @endphp
                    @foreach ($budgetCats as $budgetCat)
                        <div class="col-md-5">
                            <h4>{{ ucfirst($budgetCat) }}</h4>
                            <table class="table table-condensed">
                                <thead>
                                    <th class="text-center">Code</th>
                                    <th>Account Sector</th>
                                    {{-- <th class="text-center">Amount</th> --}}
                                    <th class="text-center">Enter Amount</th>
                                    {{-- <th class="text-center">Edit</th> --}}
                                </thead>
                                <tbody>
                                    @foreach ($sectors->where('type', $budgetCat) as $sector)
                                        <tr>
                                            <td class="text-center">{{ $sector->code}}</td>
                                            <td>{{ $sector->name }}</td>
                                            @php $amount=$sector->budgets->where('session', now()->year)->first()['amount'] @endphp
                                            <td class="text-center">
                                                <div class="input-group">
                                                    <div class="input-group-addon">$</div>
                                                    <input type=number step="any" class="form-control" id="amount" name="amount" 
                                                        placeholder="{{($amount>0)?$amount:'Amount'}}" 
                                                        value="{{($amount>0)?$amount:''}}"
                                                        required>
                                                </div>
                                            </td>
                                            {{-- <td class="text-center">
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
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    </form>
                </div>
            </div>
      </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>
    $('.datepicker').datepicker({
      format: 'yyyy',
      viewMode: "years",
      minViewMode: "years",
      autoclose:true,
    });
</script>
@endsection
