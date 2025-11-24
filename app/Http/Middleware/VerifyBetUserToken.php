<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BetUser;

class VerifyBetUserToken
{
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $token = $request->bearerToken(); // Read from Authorization: Bearer xxx

    //     // Example: Check token from DB
    //     $user = BetUser::where('api_token', $token)->first();

    //     if (!$user) {
    //         return response()->json([
    //             'message' => 'Unauthorized',
    //             'taken'=> $token
    //         ], 401);
    //     }

    //     // Optionally bind user to request
    //     $request->merge(['bet_user' => $user]);

    //     return $next($request);
    // }
}
