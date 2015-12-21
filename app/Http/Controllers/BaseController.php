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

    protected function viewAdmin($view, $data = [])
    {
        return view('admin.'.$view, $data);
    }

    protected function viewFront($view, $data = [])
    {
        return view('front.'.$view, $data);
    }
}
