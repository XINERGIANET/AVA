<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                // Redirección personalizada según el rol
                if ($user->role->nombre === 'master' || $user->role->nombre === 'admin') {
                    return redirect('/main');
                } elseif ($user->role->nombre === 'worker') {
                    return redirect('/sales');
                } else {
                    return redirect('/'); // O la ruta que prefieras para otros roles
                }
            }
        }

        return $next($request);
    }
}
