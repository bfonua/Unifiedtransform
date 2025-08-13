@extends('layouts.app')

@section('title', __('TCT Test Page'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2" id="side-navbar">
            @include('layouts.leftside-menubar')
        </div>
        <div class="col-md-10" id="main-container">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-flask"></i> TCT Test Page</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Custom Query Results</h3>
                            <p class="text-muted">This page is designed for running bespoke queries. You can modify the TestController to add your custom queries.</p>
                            
                            <!-- Sample data display -->
                            @if(isset($testData) && $testData->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($testData as $data)
                                                <tr>
                                                    <td>{{ $data->id }}</td>
                                                    <td>{{ $data->name }}</td>
                                                    <td>{{ $data->email }}</td>
                                                    <td>{{ $data->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No data available. Modify the TestController to add your custom query.
                                </div>
                            @endif

                            <!-- Custom Query Form -->
                            <div class="mt-4">
                                <h4>Custom Query Section</h4>
                                <div class="well">
                                    <p><strong>Instructions:</strong> This is where you can add forms or additional content for your bespoke queries.</p>
                                    <p>Edit the <code>TestController</code> to implement your specific query logic.</p>
                                    
                                    <!-- Example form for custom queries -->
                                    <form method="POST" action="{{ route('test.custom') }}" class="form-horizontal">
                                        @csrf
                                        <div class="form-group">
                                            <label for="query_param" class="col-sm-2 control-label">Query Parameter:</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="query_param" name="query_param" placeholder="Enter your query parameter">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-search"></i> Run Custom Query
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add any custom JavaScript for your test page here
        console.log('TCT Test page loaded');
        
        // Initialize DataTable with custom configuration
        $('.table').DataTable({
            "pageLength": 50, // Show 50 records per page instead of default 10
            "lengthMenu": [10, 25, 50, 100, 200, -1], // Allow user to choose page size, -1 means "All"
            "lengthChange": true, // Show the page length selector
            "searching": true, // Enable search
            "ordering": true, // Enable column sorting
            "info": true, // Show table info
            "autoWidth": false, // Disable auto width calculation
            "responsive": true, // Make table responsive
            "dom": 'Blfrtip', // B=buttons, l=length menu, f=filter, r=processing, t=table, i=info, p=pagination
            "buttons": [
                'copy', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection
