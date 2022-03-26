@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="panel panel-default">
                <div class="panel-body">
                    @lang('No Related Record Found.')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection