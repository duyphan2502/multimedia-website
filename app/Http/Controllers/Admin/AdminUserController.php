<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class AdminUserController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => []]);
    }
}
