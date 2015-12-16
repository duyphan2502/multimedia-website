<?php

namespace App\Http\Middleware;

use App\Models;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->ajax())
        {
            $authorization = $request->header('authorization');
            if($authorization && $authorization != 'null')
            {
                $user = Models\User::getBy(['login_token' => $authorization]);
                if($user && Carbon::now() < $user->token_expired_at)
                {
                    return $next($request);
                }
            }
            return response()->json([
                'error' => true,
                'response_code' => 401,
                'message' => 'You did not login or your session timeout.'
            ], 401);
        }

        if ($this->auth->guest())
        {
            return redirect()->guest('auth/login');
        }
        return $next($request);
    }
}