<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BetUser;

class RedirectIfAuthenticated
{
	
	public function handle(Request $request, Closure $next, ...$guards)
	{
		$guards = empty($guards) ? [null] : $guards;

		foreach ($guards as $guard) {
			if (Auth::guard($guard)->check()) {
				if ($guard === 'bet_user') {
					return redirect()->route('bet.dashboard');
				}
				return redirect(RouteServiceProvider::HOME);
			}
		}

		return $next($request);
	}
}
