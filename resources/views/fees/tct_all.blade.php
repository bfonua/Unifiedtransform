@extends('layouts.app')

@section('title', __('All Fees'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-8" id="main-container">
            <div class="panel panel-default">
                <br>
                <div class="page-panel-title">@lang('All Fees')
              </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="text-white bg-danger">{{$error}}</div>
                        @endforeach
                        <br>
                     @endif
                    @component('components.tct-fees-list',['fees'=>$fees, 'feeTypes'=>$feeTypes, 'feeChannels'=>$feeChannels])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
