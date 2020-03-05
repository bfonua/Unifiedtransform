<?php

namespace App\Http\Middleware;

use Closure;

class CheckTeacherOrStudent
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        if ($user->hasRole('teacher') || $user->hasRole('student') || $user->hasRole('admin')) {
            return $next($request);
        }

        return redirect('home');
    }
}
