<?php

namespace App\Http\Middleware;

use App\Models;

use Illuminate\Support\Facades\Auth;

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
            $authorization = substr($request->header('authorization'), 6);
            if($authorization)
            {
                $userData = json_decode(base64_decode($authorization));

                if(Auth::attempt(['email' => $userData->email, 'password' => $userData->password]))
                {
                    Auth::logout();
                    return $next($request);
                }
            }
            return response()->json([
                'error' => true,
                'response_code' => 401,
                'message' => 'You need to login to access this page.'
            ], 401);
        }

        if ($this->auth->guest())
        {
            return redirect()->guest('auth/login');
        }
        return $next($request);
    }
}