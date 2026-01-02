@extends('layouts.app')

@section('title', __('TCTNET Home'))

@section('content')    @php
        // Get school for display - handle master users
        if (Auth::user()->role == 'master' && session()->has('master_school_id')) {
            $school = \App\School::find(session('master_school_id'));
        } else {
            $school = Auth::user()->school;
        }
        
        // Get school code (fallback if not set by parent template)
        if (!isset($schoolCode)) {
            if (Auth::user()->role == 'master' && session()->has('master_school_id')) {
                $schoolCode = \App\School::find(session('master_school_id'))->code;
            } elseif (Auth::user()->school) {
                $schoolCode = Auth::user()->school->code;
            } else {
                $schoolCode = null;
            }
        }
    @endphp    <style>
        .badge-download {
            background-color: transparent !important;
            color: #464443 !important;
        }

        .list-group-item-text {
            font-size: 12px;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2" id="side-navbar">
                @include('layouts.leftside-menubar')
            </div>
            <div class="col-md-10" id="main-container">
                <div class="" style="border-top: 0px;">
                    <div class="">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="container col-md-12">
                                <br>
                                <div class="" style="">
                                    <div class="">
                                        <h2>
                                            @if ($school->name == 'Tupou College')
                                                TCTIMS Summary
                                            @else
                                                {{ $school->name }}
                                            @endif
                                        </h2>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <h4>{{ now()->year }} Student Administration</h4>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-header">Student Count</div>
                                                            <div class="card-body">
                                                                <table
                                                                    class="table table-striped table-condensed table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Type</th>
                                                                            <th class="text-right">Count</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ url('tct_users/' . $schoolCode . '/1/0') }}'">
                                                                            <td><b>Total Registered</b></td>
                                                                            <td class="text-right">{{ $totalStudents }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><span class="badge bg-success">Active</span></td>
                                                                            <td class="text-right">{{ $totalActive }}</td>
                                                                        </tr>
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ url('school/inactive') }}'">
                                                                            <td><span class="badge bg-error">Inactive</span></td>
                                                                            <td class="text-right">
                                                                                {{ $inactive = $totalStudents - $totalActive }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table
                                                                    class="table table-striped table-condensed table-hover">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                Removed
                                                                            </td>
                                                                            <td class="text-right">{{ $inactive }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Withdrawn
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $inactiveOutput['withdrawn'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Suspended
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $inactiveOutput['suspended'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Expelled
                                                                            </td>
                                                                            <td class="text-right">
                                                                                {{ $inactiveOutput['expelled'] }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <hr>
                                                                <table class="table table-striped table-condensed">
                                                                    <tbody>
                                                                        <tr>

                                                                            <td><b>Total Archived</b></td>
                                                                            <td class="text-right">
                                                                                {{ \App\StudentInfo::where('session', '<', now()->year)->count() }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-header">Form Distribution</div>
                                                            <div class="card-body pre-scrollable">
                                                                @if ($sections->first())
                                                                    <table class="table table-striped table-hover">
                                                                        <caption>Click on the form name to view students</caption>
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">Form</th>
                                                                                <th class="text-center">Active</th>
                                                                                <th class="text-center">Inactive</th>
                                                                                <th class="text-center">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($sections as $section)
                                                                                <tr>
                                                                                    <td class="text-left">
                                                                                        <a
                                                                                            href="{{ url('section/tct_students/' . $section->id . '?section=1') }}">
                                                                                            {{ $section->class->class_number . $section->section_number }}
                                                                                        </a>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span class="badge-soft-success"
                                                                                            style="font-size: 1em;">
                                                                                            <i class="material-icons"
                                                                                                style="vertical-align: middle; font-size: 1em;">check_circle</i>
                                                                                            {{ $studentCountList['active'][$section->id] }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span class="badge-soft-danger"
                                                                                            style="font-size: 1em;">
                                                                                            <i class="material-icons"
                                                                                                style="vertical-align: middle; font-size: 1em;">highlight_off</i>
                                                                                            {{ $studentCountList['total'][$section->id] - $studentCountList['active'][$section->id] }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <strong>{{ $studentCountList['total'][$section->id] }}</strong>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card bg-light mb-3">
                                                            <div class="text-left card-header">House Distribution</div>
                                                            <div class="card-body pre-scrollable">
                                                                @if ($houses->first())
                                                                    <table class="table table-striped table-hover">
                                                                        <caption>Click on the house name to view students</caption>
                                                                        <thead>
                                                                            <th class="text-center">House</th>
                                                                            <th class="text-center">Count</th>
                                                                        </thead>
                                                                        @foreach ($houses as $house)
                                                                            <tr>
                                                                                <td class="text-left">
                                                                                    <a
                                                                                        href="{{ url('house/tct_students/' . $house->id . '?section=1') }}">{{ $house->house_name }}</a>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    {{ $studentCountHouse[$house->id] }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </table>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="row panel panel-default">
                                                            <div class="page-panel-title text-center">Quick Links</div>
                                                            <div class="panel-body text-center">
                                                                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
                                                                    <a class="btn btn-sm btn-block btn-success"
                                                                        href="{{ url('register/tct_student') }}"
                                                                        data-toggle="tooltip" title="Register a new student application"> <i
                                                                            class="material-icons">person_add</i> New Application
                                                                    </a><br>
                                                                @endif
                                                                <a class="btn btn-sm btn-block btn-primary"
                                                                    href="{{ url('students/export/tct') }}"
                                                                    data-toggle="tooltip" title="Export class lists"> <i
                                                                        class="material-icons">import_export</i> Export Class Lists
                                                                </a><br>
                                                                <a class="btn btn-sm btn-block btn-primary"
                                                                    href="{{ url('students/export/house') }}"
                                                                    data-toggle="tooltip" title="Export house lists"> <i
                                                                        class="material-icons">import_export</i> Export House Lists
                                                                </a>
                                                                {{-- <a class="btn btn-sm btn-block btn-info disabled"
                                                                    href="#" aria-disabled="true"><i class="material-icons">price_check</i>
                                                                    Finance Summary
                                                                </a> --}}
                                                                <br>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>

                                                <h4>{{ now()->year }} Student Finance</h4>
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-header">Total Fees Amount by Term ($)</div>
                                                            <div class="card-body">
                                                                <table class="table table-condensed table-hover">
                                                                    <thead>
                                                                        <th class='text-center'>Type</th>
                                                                        <th class='text-right'>Term 1</th>
                                                                        <th class='text-right'>Term 2</th>
                                                                        <th class='text-right'>Term 3</th>
                                                                        <th class='text-right'>Term 4</th>
                                                                        <th class='text-right'>Late</th>
                                                                        <th class='text-right'>Total</th>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($feeArr as $fee => $feeType)
                                                                            <tr @if(strtolower($fee) == 'total') style="font-weight:bold; background:#f5f5f5;" @endif>
                                                                                <td>{{ $fee }}</td>
                                                                                <td class="text-right">{{ number_format($feeType['Term 1'] ?? 0, 2) }}</td>
                                                                                <td class="text-right">{{ number_format($feeType['Term 2'] ?? 0, 2) }}</td>
                                                                                <td class="text-right">{{ number_format($feeType['Term 3'] ?? 0, 2) }}</td>
                                                                                <td class="text-right">{{ number_format($feeType['Term 4'] ?? 0, 2) }}</td>
                                                                                <td class="text-right">
                                                                                    <span class="badge-soft-danger">
                                                                                        {{ number_format($feeType['Late Registration'] ?? 0, 2) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    <strong>{{ number_format($feeType['total'] ?? 0, 2) }}</strong>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
