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

    public function _setPageTitle($title, $subTitle = '')
    {
        view()->share([
            'pageTitle' => $title,
            'subPageTitle' => $subTitle
        ]);
    }

    public function _setBodyClass($class)
    {
        view()->share([
            'bodyClass' => $class
        ]);
    }
}