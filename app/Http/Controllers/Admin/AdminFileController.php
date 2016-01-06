<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Console\Application;
use Illuminate\Http\Request;

use App\Models;

class AdminFileController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->app = app();
        $this->package = 'elfinder';
    }

    public function getFileManager(Request $request, $user_id = null)
    {
        return $this->viewAdmin('file.file-manager', $this->getViewVars($user_id));
    }

    protected function getViewVars($user_id = null)
    {
        $dir = 'packages/barryvdh/'.$this->package;
        $locale = $this->app->config->get('app.locale');
        if (!file_exists($this->app['path.public'] . "/$dir/js/i18n/elfinder.$locale.js")) {
            $locale = false;
        }
        $csrf = true;

        $url = $this->app->config->get('app.adminCpAccess').'/files/connector';
        if($user_id) $url .= '/'.$user_id;
        $url = asset($url);

        return compact('dir', 'locale', 'csrf', 'url');
    }

    public function anyConnector($user_id = null)
    {
        $roots = $this->app->config->get('elfinder.roots', []);
        if (empty($roots)) {
            $dirs = (array) $this->app['config']->get('elfinder.dir', []);

            if($user_id != null && $user_id > 0)
            {
                //$dirs[0];
                if(!is_dir($dirs[0].DIRECTORY_SEPARATOR.md5($user_id))){
                    mkdir($dirs[0].DIRECTORY_SEPARATOR.md5($user_id), 0777, true);
                }
            }

            foreach ($dirs as $dir) {
                $path = $dir;
                $url = $dir;
                if($user_id != null && $user_id > 0)
                {
                    $path = $dir.DIRECTORY_SEPARATOR.md5($user_id);
                    $url = $dir.'/'.md5($user_id);
                }
                $roots[] = [
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => public_path($path), // path to files (REQUIRED)
                    'URL' => url($url), // URL to files (REQUIRED)
                    'accessControl' => $this->app->config->get('elfinder.access') // filter callback (OPTIONAL)
                ];
            }

            $disks = (array) $this->app['config']->get('elfinder.disks', []);
            foreach ($disks as $key => $root) {
                if (is_string($root)) {
                    $key = $root;
                    $root = [];
                }
                $disk = app('filesystem')->disk($key);
                if ($disk instanceof FilesystemAdapter) {
                    $defaults = [
                        'driver' => 'Flysystem',
                        'filesystem' => $disk->getDriver(),
                        'alias' => $key,
                    ];
                    $roots[] = array_merge($defaults, $root);
                }
            }
        }

        $opts = $this->app->config->get('elfinder.options', array());

        $temp = ['roots' => $roots];

        foreach($opts['roots'] as $key => $row)
        {
            foreach($opts['roots'][$key] as $keyChild => $rowChild)
            {
                if(!isset($temp['roots'][$key][$keyChild]))
                {
                    $temp['roots'][$key][$keyChild] = $rowChild;
                }

            }
        }

        $opts = $temp;

        // run elFinder
        $connector = new \Barryvdh\Elfinder\Connector(new \elFinder($opts));
        $connector->run();
        return $connector->getResponse();
    }
}