<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class AdminDashboardController extends BaseAdminController
{
    var $bodyClass = 'dashboard-controller dashboard-page';
    public function __construct()
    {
        parent::__construct();

        $this->_setPageTitle('Dashboard', 'dashboard & statistics');
        $this->_setBodyClass($this->bodyClass);

        $this->loadAdminMenu('dashboard');
    }

    public function getIndex(Request $request)
    {
        return $this->viewAdmin('dashboard.index');
    }
}