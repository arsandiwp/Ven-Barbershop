<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AuthenticationService;

class AuthenticationRedis
{
    private $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        $userData = $this->isTokenExist($token);

        if (!$userData) {
            return response('Invalid login token', 401);
        }

        $request['_session'] = $userData;
        return $next($request);
    }

    private function isTokenExist($token)
    {
        if (!$userData = $this->authenticationService->getTokenData($token)) {
            return false;
        }

        return $userData;
    }



}
