@php
    // Get school code - for master users, get from session, otherwise from user's school
    if (Auth::user()->role == 'master' && session()->has('master_school_id')) {
        $schoolCode = \App\School::find(session('master_school_id'))->code;
    } elseif (Auth::user()->school) {
        $schoolCode = Auth::user()->school->code;
    } else {
        $schoolCode = null;
    }
@endphp

<script>
    $(document).ready(function() {
        $('.nav-item.active').removeClass('active');
        $('a[href="' + window.location.href + '"]').closest('li').closest('ul').closest('li').addClass(
            'active');
        $('a[href="' + window.location.href + '"]').closest('li').addClass('active');
    });
</script>

<style>
    .nav-item.active {
        background-color: #fce8e6;
        font-weight: bold;
    }

    .nav-item.active a {
        color: #d93025;
    }

    .nav-link-text {
        padding-left: 10%;
    }

    #side-navbar ul>li>a {
        padding: 8px 15px;
    }
</style>

<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="{{ url('home') }}"><i class="material-icons">dashboard</i> <span
                class="nav-link-text">@lang('Home')</span></a>
    </li>
    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('register/tct_student') }}"><i class="material-icons">group_add</i> <span
                    class="nav-link-text">@lang('New Student Form')</span></a>
        </li>
    @endif
    <li class="nav-item" style="border-bottom: 2px solid #dbd8d8;"></li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('school/sections?course=1') }}"><i class="material-icons">class</i> <span
                class="nav-link-text">@lang('Student Classes')</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('school/houses') }}">
            <i class="material-icons">house</i> <span class="nav-link-text">@lang('Student Houses')</span></a>
    </li>
    <li class="nav-item dropdown">
        <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"><i class="material-icons">monetization_on</i> <span class="nav-link-text">@lang('Student Fees')</span> <i
                class="material-icons pull-right">keyboard_arrow_down</i></a>
        <ul class="dropdown-menu" style="width: 100%;">
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('fees/assign') }}"><i class="material-icons">monetization_on</i>
                    <span class="nav-link-text">@lang('Student Fees - Current')</span></a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('fees/assigned/2025') }}"><i class="material-icons">history</i>
                    <span class="nav-link-text">@lang('Student Fees - 2025')</span></a>
            </li>
        </ul>
    </li>
    <li class="nav-item dropdown">
        <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"><i class="material-icons">library_books</i> <span class="nav-link-text">Student
                Subjects</span> <i class="material-icons pull-right">keyboard_arrow_down</i></a>
        <ul class="dropdown-menu" style="width: 100%;">
            <li>
                <a class="dropdown-item" href="{{ url('subject/section') }}"><i class="material-icons">toc</i>
                    <span class="nav-link-text">By Subject</span></a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ url('subject/subject-assign-class') }}"><i
                        class="material-icons">toc</i> <span class="nav-link-text">By Form</span></a>
            </li>
        </ul>
    </li>
    <li class="nav-item" style="border-bottom: 2px solid #dbd8d8;"></li>


    <li class="nav-item dropdown">
        <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"><i class="material-icons">contacts</i> <span
                class="nav-link-text">@lang('Student Lists')</span> <i
                class="material-icons pull-right">keyboard_arrow_down</i></a>
        <ul class="dropdown-menu" style="width: 100%;">
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('tct_users/' . $schoolCode . '/1/0') }}"><i
                        class="material-icons">group</i><span class="nav-link-text">@lang('Registered')</span></a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('school/inactive') }}"><i class="material-icons">group</i><span
                        class="nav-link-text">@lang('Inactive')</span></a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('prefects/tct_students') }}"><i
                        class="material-icons">group</i><span class="nav-link-text">@lang('Prefects')</span></a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('tct_users_archive') }}"><i class="material-icons">group</i><span
                        class="nav-link-text">@lang('Archived')</span></a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="{{ url('other/tct_students') }}"><i
                        class="material-icons">assignment</i><span class="nav-link-text">@lang('Church / Nationality')</span></a>
            </li>
        </ul>
    </li>

    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <li class="nav-item dropdown">
            <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i class="material-icons">class</i> <span
                    class="nav-link-text">@lang('Manage Classes')</span> <i
                    class="material-icons pull-right">keyboard_arrow_down</i></a>
            <ul class="dropdown-menu" style="width: 100%;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('school/inactive_sections') }}"><i
                            class="material-icons">class</i>
                        <span class="nav-link-text">@lang('Inactive Sections')</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('school/sections_by_year') }}"><i
                            class="material-icons">class</i>
                        <span class="nav-link-text">@lang('Sections by Year')</span></a>
                </li>
            </ul>
        </li>
    @endif
    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master' || Auth::user()->role == 'accountant')
        <li class="nav-item dropdown">
            <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i class="material-icons">monetization_on</i> <span
                    class="nav-link-text">@lang('Manage Fees')</span> <i
                    class="material-icons pull-right">keyboard_arrow_down</i></a>
            <ul class="dropdown-menu" style="width: 100%;">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('fees/fee_type') }}"><i class="material-icons">dynamic_feed</i>
                        <span class="nav-link-text">@lang('Fee Type')</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('fees/fee_channel') }}"><i class="material-icons">toc</i> <span
                            class="nav-link-text">@lang('Fee Channel')</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('fees/tct_all') }}"><i class="material-icons">attach_money</i>
                        <span class="nav-link-text">@lang('All Fees')</span></a>
                </li>
            </ul>
        </li>
    @endif
    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <li class="nav-item">
            @php
                $count = \App\StudentInfo::where('session', now()->year)->where('assigned', 0)->count('id');
            @endphp
            <a class="nav-link" href="{{ url('fees/unassign') }}"><i class="material-icons">assignment_late</i>
                <span class="nav-link-text">@lang('Unassigned Fees')</span><span
                    class="badge pull-right">&nbsp{{ $count }}&nbsp</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ url('subject') }}"><i class="material-icons">library_books</i> <span
                    class="nav-link-text">Manage Subjects</span>
            </a>
        </li>
        <li class="nav-item" style="border-bottom: 2px solid #dbd8d8;"></li>
    @endif
    
    @if (Auth::user()->role == 'master')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.index') }}"><i class="material-icons">settings</i> <span
                    class="nav-link-text">@lang('Academic Settings')</span></a>
        </li>
    @endif
    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <li class="nav-item dropdown disabled">
            <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i class="material-icons">chrome_reader_mode</i> <span
                    class="nav-link-text">@lang('Manage GPA')</span> <i
                    class="material-icons pull-right">keyboard_arrow_down</i></a>
            <ul class="dropdown-menu" style="width: 100%;">
                <li>
                    <a class="dropdown-item" href="{{ url('gpa/all-gpa') }}"><i
                            class="material-icons">developer_board</i> <span
                            class="nav-link-text">@lang('All GPA')</span></a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('gpa/create-gpa') }}"><i
                            class="material-icons">note_add</i> <span
                            class="nav-link-text">@lang('Add New GPA')</span></a>
                </li>
            </ul>
        </li>
    @endif
    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'accountant' || Auth::user()->role == 'master')
        <li class="nav-item dropdown">
            <ul class="dropdown-menu" style="width: 100%;">
                <li>
                    <a class="dropdown-item" href="{{ url('fees/all') }}"><i
                            class="material-icons">developer_board</i> <span
                            class="nav-link-text">@lang('Generate Form')</span></a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('fees/create') }}"><i class="material-icons">note_add</i>
                        <span class="nav-link-text">@lang('Add Fee Field')</span></a>
                </li>
            </ul>
        </li>
    @endif

    @if (Auth::user()->role == 'student')
        <li class="nav-item">
            <a class="nav-link active" href="{{ url('attendances/0/' . Auth::user()->id . '/0') }}"><i
                    class="material-icons">date_range</i>
                <span class="nav-link-text">@lang('My Attendance')</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('courses/0/' . Auth::user()->section_id) }}"><i
                    class="material-icons">subject</i>
                <span class="nav-link-text">@lang('My Courses')</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('grades/' . Auth::user()->id) }}"><i
                    class="material-icons">bubble_chart</i> <span class="nav-link-text">@lang('My Grade')</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('stripe/charge') }}"><i class="material-icons">payment</i> <span
                    class="nav-link-text">@lang('Payment')</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('stripe/receipts') }}"><i class="material-icons">receipt</i> <span
                    class="nav-link-text">@lang('Receipt')</span></a>
        </li>
    @endif

    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'master')
        <li class="nav-item dropdown disabled">
            <a role="button" href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i class="material-icons">local_library</i> <span
                    class="nav-link-text">@lang('Manage Library')</span> <i
                    class="material-icons pull-right">keyboard_arrow_down</i></a>
            <ul class="dropdown-menu" style="width: 100%;">
                <li>
                    <a class="dropdown-item" href="{{ route('library.books.index') }}"><i
                            class="material-icons">developer_board</i>
                        <span class="nav-link-text">@lang('All Books')</span></a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('library/issued-books') }}"><i
                            class="material-icons">developer_board</i>
                        <span class="nav-link-text">@lang('All Issued Books')</span></a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ url('library/issue-books') }}"><i
                            class="material-icons">receipt</i> <span
                            class="nav-link-text">@lang('Issue Book')</span></a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('library.books.create') }}"><i
                            class="material-icons">note_add</i> <span
                            class="nav-link-text">@lang('Add New Book')</span></a>
                </li>
            </ul>
        </li>
    @endif

    @if (Auth::user()->role == 'master')
        <li class="nav-item" style="border-top: 2px solid #dbd8d8;">
            <a class="nav-link" href="{{ route('test.index') }}"><i class="material-icons">build</i>
                <span class="nav-link-text">TCT Test Page</span></a>
        </li>
    @endif
</ul>
