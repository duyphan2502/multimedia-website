<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

class AdminSettingController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'error' => true,
            'response_code' => 500
        ];
    }

    public function getIndex(Request $request)
    {
        $settings = Models\Setting::getAllSettings();
        $this->data = [
            'error' => false,
            'response_code' => 200,
            'data' => $settings
        ];
        return response()->json($this->data, $this->data['response_code']);
    }

    public function putUpdateAll(Request $request)
    {
        $result = Models\Setting::updateSettings($request->all());
        return response()->json($result, $result['response_code']);
    }
}
