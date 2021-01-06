@extends('layouts.app')

@section('title', __('All Fees'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="panel panel-default">
                <br>
                <div class="page-panel-title">@lang('All Fees')
                
                  {{-- <button class="btn btn-xs btn-success pull-right" role="button" id="btnPrint" ><i class="material-icons">print</i> @lang('Print Fees Form')</button> --}}
              </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="text-white bg-danger">{{$error}}</div>
                            {{-- <span class="error">{{ $error}}</span> --}}
                        @endforeach
                        <br>
                     @endif
                     
                     {{$fees->links()}}
                     <br>
                    @component('components.tct-fees-list',['fees'=>$fees])
                    @endcomponent
                    {{$fees->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
