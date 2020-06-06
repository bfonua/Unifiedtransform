@extends('layouts.app')

@section('title', __('Other Distributions'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <h3>Other Distributions</h3>
                <div class="row">
                    <div class="col-md-4">
                        @if($churches->first())
                            <div class="card">
                                <div class="card-header text-center">Church Distribution</div>
                                <div class="card-body">
                                    <table class="table table-condensed">
                                        @foreach($churches as $church)
                                            <tr>
                                                <td>{{$church->church}}</td>
                                                <td class="text-right">{{$church->count}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @else
                            <div>No Related Data Found.</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if($churches->first())
                            <div class="card">
                                <div class="card-header text-center">Village Distribution</div>
                                <div class="card-body">
                                    <table class="table table-condensed">
                                        @foreach($villages as $village)
                                            <tr>
                                                <td>{{$village->village}}</td>
                                                <td class="text-right">{{$village->count}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @else
                            <div>No Related Data Found.</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if($churches->first())
                            <div class="card">
                                <div class="card-header text-center">Country Distribution</div>
                                <div class="card-body">
                                    <table class="table table-condensed">
                                        @foreach($countries as $country)
                                            <tr>
                                                <td>{{$country->nationality}}</td>
                                                <td class="text-right">{{$country->count}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @else
                            <div>No Related Data Found.</div>
                        @endif
                    </div>
                </div>
        </div>

    </div>
</div>
@endsection

@section('jsFiles')
    <script>
        $(document).ready(function($){
            $('#myTable').DataTable({
                paging: false,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
