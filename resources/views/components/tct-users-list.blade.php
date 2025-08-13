<div class="table-responsive">
<table id='myTable' class="table table-bordered table-condensed table-striped table-hover">
  <thead>
    <tr>
        <th scope="col" class="text-center">TCT ID</th>
        <th scope="col" class="text-center">@lang('Status')</th>
        <th scope="col" class="text-center">@lang('Full Name')</th>
        @if($type != 'registered')
        <th scope="col" class="text-center">@lang('Session')</th>
        @endif
        <th scope="col" class="text-center">@lang('Form')</th>
        <th scope="col" class="text-center">@lang('Form #')</th>
        <th scope="col" class="text-center">@lang('House')</th>
        <th scope="col" class="text-center">@lang('Church')</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($users as $user)
        <tr class="bg-">
            <td class="text-center"><small>{{ $user->student_code }}</small></td>
            <td class="text-center">
                @if($type == 'registered')
                    {{ ($user->active && optional($user->studentInfo)->group) ? ucfirst($user->studentInfo->group) : 'Inactive' }}
                @elseif($type == 'archived')
                    {{ ($user->active && optional($user->studentInfo)->group) ? 'Graduated / ' . ucfirst($user->studentInfo->group) : 'Inactive' }}
                @endif
            </td>
            <td>
                <small>
                    <a href="{{ url('user/' . $user->student_code) }}">
                        {{ $user->given_name == '' ? $user->name : $user->given_name . ' ' . $user->lst_name }}
                    </a>
                </small>
            </td>
            @if($type != 'registered')
                <td class="text-center">
                    <small>{{ optional($user->studentInfo)->session }}</small>
                </td>
            @endif
            <td class="text-center">
                <small>
                    {{ optional(optional(optional($user->studentInfo)->section)->class)->class_number ?? '-' }}{{ optional($user->studentInfo->section)->section_number ?? '' }}
                </small>
            </td>
            <td class="text-center"><small>{{ optional($user->studentInfo)->form_num }}</small></td>
            <td class="text-center" style="white-space: nowrap;">
                <small>{{ optional($user->studentInfo->house)->house_name }}</small>
            </td>
            <td class="text-center"><small>{{ optional($user->studentInfo)->church }}</small></td>
        </tr>
    @endforeach
  </tbody>
</table>
</div>

@section('jsFiles')
    <script>
        $(document).ready(function($){
            $('#myTable').DataTable({
                "pageLength": 50, // Show 50 records per page instead of default 10
                "lengthMenu": [10, 25, 50, 100, 200, -1], // Allow user to choose page size, -1 means "All"
                "lengthChange": true, // Show the page length selector dropdown
                // paging: false, // Uncomment this line if you want to disable paging entirely
                dom: 'Blfrtip', // Added 'l' for length menu
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });
        });
    </script>
@endsection
