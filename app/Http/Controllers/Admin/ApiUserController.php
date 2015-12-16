<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class ApiUserController extends BaseAdminController
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
