<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Models;

abstract class BaseController extends Controller
{
    public function __construct()
    {
        $this->settings = Models\Setting::getAllSettings();

        $this->defaultLanguage = (isset($this->settings['default_language']) ? $this->settings['default_language'] : 59);


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
