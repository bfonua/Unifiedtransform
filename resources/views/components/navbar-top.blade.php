<nav class="navbar navbar-inverse navbar-static-top" style="background-color: #23262f;">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-text" href="{{ url('/home')}}">TCTIMS</a>
        </div>
        <form class="navbar-form navbar-right" action="/action_page.php">
            @guest
            @else
                <div x-data="searchComponent()" class="form-group" style="min-width: 300px">
                    <input x-model="searchQuery" @input="fetchResults()" id="search-input" type="text"
                        class="form-control search-input" placeholder="@lang('Search Name or TCTID')" autocomplete="off">
                    <button type="button" class="btn btn-default" @click="clearSearch()">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <!-- Results dropdown -->
                    <ul x-show="results.length > 0" class="list-group" @click.away="results = []"
                        style="position: absolute; z-index: 50; max-height: 300px; overflow-y: auto; left: auto;">
                        <template x-for="result in results" :key="result.id">
                            <li class="list-group-item">
                                <a :href="'/user/' + result.student_code">
                                    <strong x-text="result.student_code"></strong>
                                    <span x-text="result.given_name + ' ' + result.lst_name"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                    <!-- No results found -->
                    <div x-show="!loading && results.length === 0 && searchQuery.length > 0"
                        class="list-group"
                        style="position: absolute; z-index: 50;">
                        <li class="list-group-item">
                            @lang('No results found')
                        </li>
                    </div>
                    <div x-show="loading" class="loading-spinner"></div>
                </div>
            @endif
        </form>
        <ul class="nav navbar-nav navbar-right">
            @guest
                <li><a href="{{ route('login') }}" ">@lang('Login')</a></li>
            @else
            @if(app('impersonate')->isImpersonating())
            <li>
                <span class="label label-warning" style="padding: 8px; margin-top: 15px; display: inline-block;">
                    <i class="glyphicon glyphicon-eye-open"></i> @lang('Impersonating')
                </span>
            </li>
            @endif
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <span class="label label-danger">
                        {{ ucfirst(\Auth::user()->role) }} 
                    </span>
                    &nbsp;&nbsp; &nbsp;&nbsp;{{ Auth::user()->name }}</span>
                <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    @if (Auth::user()->role == 'master')
                        @php
                            $masterSchoolId = session()->has('master_school_id') ? session('master_school_id') : null;
                        @endphp
                        @if(session()->has('master_school_id'))
                            <li>
                                <a href="{{ url('master/leave-school') }}">@lang('Leave School Portal')</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('masters.index') }}">@lang('Manage Schools')</a>
                            </li>
                        @endif
                    @endif
                    @if ((Auth::user()->role == 'master' && session()->has('master_school_id')) || Auth::user()->role == 'admin')
                        @php
                            $schoolId = Auth::user()->role == 'master' ? session('master_school_id') : Auth::user()->school_id;
                        @endphp
                        <li>
                            <a href="{{ url('school/admin-list/' . $schoolId) }}">@lang('View Admins')</a>
                        </li>
                        <li>
                            <a href="{{ url('school/non-student-users/' . $schoolId) }}">@lang('View Non-Student Users')</a>
                        </li>
                    @endif
                    @if (Auth::user()->role == 'teacher' || Auth::user()->role == 'accountant' || Auth::user()->role == 'admin')
                        <li>
                            <a href="{{ url('user/profile/edit') }}">@lang('Edit Profile')</a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ url('user/config/change_password') }}">@lang('Change Password')</a>
                    </li>
                    @if (env('APP_ENV') != 'production' && (app('impersonate')->isImpersonating() || Auth::user()->role == 'master'))
                        <li>
                            <a href="{{ url('user/config/impersonate') }}">
                                {{ app('impersonate')->isImpersonating() ? __('Leave Impersonation') : __('Impersonate') }}
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            @lang('Logout')
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>
</nav>
<script>
    function searchComponent() {
        return {
            searchQuery: '',
            results: [],
            loading: false,
            clearSearch() {
                this.searchQuery = '';
                this.results = [];
                document.getElementById('search-input').focus();
            },
            fetchResults() {
                if (this.searchQuery.trim().length < 1) {
                    this.results = [];
                    this.loading = false;
                    return;
                }
                this.loading = true;
                fetch(`/find?q=${encodeURIComponent(this.searchQuery.trim())}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        this.results = data;
                    })
                    .catch(err => {
                        console.error('Autocomplete fetch error:', err);
                        this.results = [];
                    })
                    .finally(() => {
                        this.loading = false;
                    }
                );
            }
        }
    }
</script>
