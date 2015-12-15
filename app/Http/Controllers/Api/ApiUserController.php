<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Models;

class ApiUserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['postAuthenticate']]);
    }

    public function postAuthenticate(Request $request)
    {
        return $this->authenticateUser($request);
    }
}
