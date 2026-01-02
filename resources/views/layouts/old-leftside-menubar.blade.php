{{-- Old menubar details to be considered for future modules - DO NOT USE OR DELETE --}}

@if (Auth::user()->role == 'admin' || Auth::user()->role == 'master' || Auth::user()->role == 'master')
    <li class="nav-item disabled">
        <a class="nav-link" href="{{ url('grades/all-exams-grade') }}"><i class="material-icons">assignment</i>
            <span class="nav-link-text">@lang('Grades')</span></a>
    </li>
    <li class="nav-item disabled">
        <a class="nav-link" href="{{ url('academic/routine') }}"><i class="material-icons">calendar_today</i>
            <span class="nav-link-text">@lang('Class Routine')</span></a>
    </li>
    <li class="nav-item disabled">
        <a class="nav-link" href="{{ url('academic/syllabus') }}"><i class="material-icons">vertical_split</i>
            <span class="nav-link-text">@lang('Syllabus')</span></a>
    </li>
    <li class="nav-item disabled">
        <a class="nav-link" href="{{ url('academic/notice') }}"><i class="material-icons">announcement</i> <span
                class="nav-link-text">@lang('Notice')</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('academic/event') }}"><i class="material-icons">event</i> <span
                class="nav-link-text">@lang('Event')</span></a>
    </li>
    <li class="nav-item" style="border-bottom: 1px solid #dbd8d8;"></li>
@endif

<a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
aria-expanded="false"><i class="material-icons">monetization_on</i> <span
    class="nav-link-text">@lang('Fees Generator')</span> <i
    class="material-icons pull-right">keyboard_arrow_down</i>
</a>

@if (Auth::user()->role == 'admin' || Auth::user()->role == 'accountant' || Auth::user()->role == 'master')
    <li class="nav-item dropdown">
        <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"><i class="material-icons">account_balance_wallet</i> <span
                class="nav-link-text">@lang('Manage Accounts')</span> <i
                class="material-icons pull-right">keyboard_arrow_down</i></a>
        <ul class="dropdown-menu" style="width: 100%;">
            <li>
                <a class="dropdown-item"
                    href="{{ url('users/' . $schoolCode . '/accountant') }}"><i
                        class="material-icons">account_balance_wallet</i>
                    <span class="nav-link-text">@lang('Accountant List')</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('accounts/sectors') }}"><i
                        class="material-icons">developer_board</i>
                    <span class="nav-link-text">@lang('Add Account Sector')</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('accounts/expense') }}"><i
                        class="material-icons">note_add</i> <span
                        class="nav-link-text">@lang('Add New Expense')</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('accounts/expense-list') }}"><i
                        class="material-icons">developer_board</i>
                    <span class="nav-link-text">@lang('Expense List')</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('accounts/income') }}"><i
                        class="material-icons">note_add</i> <span
                        class="nav-link-text">@lang('Add New Income')</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('accounts/income-list') }}"><i
                        class="material-icons">developer_board</i>
                    <span class="nav-link-text">@lang('Income List')</span></a>
            </li>
        </ul>
    </li>
@endif