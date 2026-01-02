<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdminMasterAccountantTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        if ($user->hasRole('admin') || $user->hasRole('master') || $user->hasRole('accountant') || $user->hasRole('teacher')) {
            return $next($request);
        }
        return redirect('home');
    }
}
