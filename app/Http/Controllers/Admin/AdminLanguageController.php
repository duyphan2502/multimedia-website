<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class AdminLanguageController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->data = [
            'error' => true,
            'response_code' => 500
        ];
    }

    public function getIndex(Request $request)
    {
        $language = Models\Language::getAllLanguage();
        $this->data = [
            'error' => false,
            'response_code' => 200,
            'data' => $language->toArray()
        ];
        return response()->json($this->data, $this->data['response_code']);
    }
}
