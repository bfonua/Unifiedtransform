<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Maximum login attempts before lockout
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * Lockout duration in minutes
     * @var int
     */
    protected $decayMinutes = 15;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('throttle:5,1')->only('login'); // 5 attempts per minute
    }

    public function redirectTo()
    {
        $role = Auth::user()->role;

        return ($role == 'master')?'/masters':'/home';
    }

    public function username()
    {
        $login = request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        request()->merge([$field => $login]);
        return $field;
    }

    public function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials = array_add($credentials, 'active', '1');
        return $credentials;
    }

    /**
     * Validate login request with additional security
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * Handle failed login attempt
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Log failed attempt
        \Log::warning('Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log successful login
     */
    protected function authenticated(Request $request, $user)
    {
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);
    }

    /**
     * Override logout to log the action
     */
    public function logout(Request $request)
    {
        \Log::info('User logged out', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
        ]);

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
