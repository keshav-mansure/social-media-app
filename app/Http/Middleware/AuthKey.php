<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthKey
{

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('authKey');
        if ($token == 'HELLOWORLD') {
            return $next($request);
        }
        return response()->json('Unauthenticated', 401);
    }
}
