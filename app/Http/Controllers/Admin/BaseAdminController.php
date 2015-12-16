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

    public function authenticateUser(Request $request, $data = null)
    {
        $user = Models\User::getUserByEmail($request->get('email'));
        /*If user not exists or wrong password*/
        if(!$user || !\Hash::check($request->get('password'), $user->password))
        {
            return response()->json($this->data, $this->data['response_code']);
        }
        /*Save token*/
        if(!$user->login_token)
        {
            $user->login_token = md5($user->id.$user->email.time());
        }
        $user->token_expired_at = Carbon::now()->addHour(24);
        $user->save();

        $this->data = [
            'error' => false,
            'response_code' => 200,
            'access_token' => $user->login_token
        ];
        return response()->json($this->data, $this->data['response_code']);
    }
}