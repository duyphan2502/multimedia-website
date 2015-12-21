<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class AdminFileController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFileManager(Request $request)
    {
        return $this->viewAdmin('files.file-manager');
    }
}