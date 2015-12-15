<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $this->data = [
            'error' => false,
            'response_code' => 200
        ];
        return response()->json($this->data, $this->data['response_code']);
    }
}
