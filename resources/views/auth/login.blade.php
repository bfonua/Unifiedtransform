@extends('layouts.app')

@section('title', __('Login'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="main-container">
            <div class="panel panel-default">
                <br>
                <div class="page-panel-title">@lang('Login')</div>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}" id="loginForm">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">@lang('E-Mail Or Phone Number')</label>

                            <div class="col-md-6">
                                <input id="email" 
                                       type="text" 
                                       class="form-control" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus
                                       autocomplete="username"
                                       maxlength="100">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('Password')</label>

                            <div class="col-md-6">
                                <input id="password" 
                                       type="password" 
                                       class="form-control" 
                                       name="password" 
                                       required
                                       autocomplete="current-password"
                                       minlength="6"
                                       maxlength="255">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                <small class="help-block text-muted">
                                    @lang('Minimum 6 characters')
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('Remember Me')
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" id="loginBtn">
                                    @lang('Login')
                                </button>
                                {{--
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    @lang('Forgot Your Password?')
                                </a>
                                --}}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <small class="text-muted">
                                    <i class="fa fa-shield"></i> @lang('Your connection is secure and encrypted')
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prevent multiple form submissions
document.getElementById('loginForm').addEventListener('submit', function(e) {
    var btn = document.getElementById('loginBtn');
    if (btn.disabled) {
        e.preventDefault();
        return false;
    }
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> @lang("Logging in...")';
});

// Clear sensitive data on page unload
window.addEventListener('beforeunload', function() {
    document.getElementById('password').value = '';
});
</script>
@endsection
