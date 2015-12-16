<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Models;

use App\Models\Page;
use App\Models\PageContent;
use Illuminate\Support\Facades\Auth;

class BaseAdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }
}