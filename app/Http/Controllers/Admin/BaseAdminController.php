<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Models;

use Carbon\Carbon;

abstract class BaseAdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    public function authenticateUser(Request $request)
    {
        $result = [
            'error' => true,
            'response_code' => 401,
            'message' => 'Wrong password or email'
        ];
        $user = Models\User::getUserByEmail($request->get('email'));
        /*If user not exists or wrong password*/
        if(!$user || !\Hash::check($request->get('password'), $user->password))
        {
            return response()->json($result, $result['response_code']);
        }
        /*Save token*/
        if(!$user->login_token)
        {
            $user->login_token = md5($user->id.$user->email.time());
        }
        $user->token_expired_at = Carbon::now()->addHour(24);
        $user->save();

        $result = [
            'error' => false,
            'response_code' => 200,
            'access_token' => $user->login_token,
            'message' => 'Login successful.'
        ];
        return response()->json($result, $result['response_code']);
    }
}