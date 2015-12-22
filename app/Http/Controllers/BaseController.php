<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models;

abstract class BaseController extends Controller
{
    protected $adminCpAccess;

    protected $loggedInUser = null;

    protected $errorMessages = [], $infoMessages = [], $successMessages = [], $warningMessages = [];
    
    protected function __construct()
    {
        $this->adminCpAccess = \Config::get('app.adminCpAccess');
        $this->settings = Models\Setting::getAllSettings();

        $this->defaultLanguageId = (isset($this->settings['default_language']) ? $this->settings['default_language'] : 59);

        /*Get logged in user*/
        if(auth()->user())
        {
            $this->loggedInUser = auth()->user();
            view()->share('loggedInUser', $this->loggedInUser);
            //$this->loggedInUserRole = $this->loggedInUser->userRole->slug;
        }
    }

    protected function viewAdmin($view, $data = [])
    {
        return view('admin.'.$view, $data);
    }

    protected function viewFront($view, $data = [])
    {
        return view('front.'.$view, $data);
    }

    protected function _showErrorPage($errorCode = 404, $message = null)
    {
        return abort($errorCode, $message);
    }

    protected function _setFlashMessage($message, $type)
    {
        $model = 'infoMessages';
        switch($type)
        {
            case 'info':
            {
                $model = 'infoMessages';
            } break;
            case 'error':
            {
                $model = 'errorMessages';
            } break;
            case 'danger':
            {
                $model = 'errorMessages';
            } break;
            case 'success':
            {
                $model = 'successMessages';
            } break;
            case 'warning':
            {
                $model = 'warningMessages';
            } break;
        }
        if(is_array($message))
        {
            foreach($message as $key => $value)
            {
                array_push($this->$model, $value);
            }
        }
        else
        {
            array_push($this->$model, $message);
        }
    }

    protected function _showFlashMessages()
    {
        Session::flash('errorMessages', $this->errorMessages);
        Session::flash('infoMessages', $this->infoMessages);
        Session::flash('successMessages', $this->successMessages);
        Session::flash('warningMessages', $this->warningMessages);
    }

    protected function _getFlashMessages()
    {
        return [
            'errorMessages' => $this->errorMessages,
            'infoMessages' => $this->infoMessages,
            'successMessages' => $this->successMessages,
            'warningMessages' => $this->warningMessages,
        ];
    }
}