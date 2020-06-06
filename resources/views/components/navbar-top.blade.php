<nav class="navbar navbar-inverse navbar-static-top" style="background-color: #23262f;">
    <div class="container-fluid">
        <div class="navbar-header">
            {{-- <a class="navbar-brand"href="{{ url('/home') }}" style="color: #000";>TCTNET</a> --}}
            <p class="navbar-text">TCTNET</p>
        </div>
        <form class="navbar-form navbar-right" action="/action_page.php">
            @guest
                
            @else
                <div class="input-group">
                    <input id="search-input" type="text" class="form-control search-input typeahead" placeholder="Search Name or TCTID">
                    {{-- <div class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                        <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div> --}}
                </div>
            @endif
        </form>
        <ul class="nav navbar-nav navbar-right">
            @guest
                <li><a href="{{ route('login') }}" ">@lang('Login')</a></li>
            @else
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="label label-danger">
                            {{ ucfirst(\Auth::user()->role) }}
                        </span>
                        &nbsp;&nbsp; &nbsp;&nbsp;{{ Auth::user()->name }}</span>
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @if(Auth::user()->role != 'master')
                            <li>
                                <a href="{{url('user/'.Auth::user()->student_code)}}">@lang('Profile')</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{url('user/config/change_password')}}">@lang('Change Password')</a>
                            </li>
                            @if(env('APP_ENV') != 'production')
                            <li>
                                <a href="{{url('user/config/impersonate')}}">
                                    {{ app('impersonate')->isImpersonating() ? __('Leave Impersonation') : __('Impersonate') }}
                                </a>                                
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();
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
