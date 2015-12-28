<?php namespace App\Http\Controllers\Admin;

use Acme;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Models;

use Carbon\Carbon;

abstract class BaseAdminController extends BaseController
{
    var $loggedInAdminUser;
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');

        $this->loggedInAdminUser = $this->_getLoggedInAdminUser();
        view()->share(['loggedInAdminUser' => $this->loggedInAdminUser]);

        $this->loadAdminMenu();

        /*Get logged in user*/
        if(auth()->user())
        {
            $this->loggedInUser = auth()->user();
            view()->share('loggedInUser', $this->loggedInUser);
            //$this->loggedInUserRole = $this->loggedInUser->userRole->slug;
        }
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

    protected function loadAdminMenu($menuActive = '')
    {
        $currentUser = $this->loggedInUser;

//        $userRoles = ($currentUser->userRole->slug == 'webmaster') ? $currentUser->userRole->slug : array();
//        if(is_array($userRoles))
//        {
//            $userRoles = $currentUser->menuNode()->getRelatedIds()->toArray();
//        }
        $menu = new Acme\CmsMenu();
        $menu->args = array(
            'menuName' => 'admin-menu',
            'menuClass' => 'page-sidebar-menu page-header-fixed',
            'container' => 'div',
            'containerClass' => 'page-sidebar navbar-collapse collapse',
            'containerId' => '',
            'containerTag' => 'ul',
            'childTag' => 'li',
            'itemHasChildrenClass' => '',
            'subMenuClass' => 'sub-menu',
            'menuActive' => [
                'type' => 'customLink',
                'related_id' => $menuActive
            ],
            'activeClass' => 'active',
            //'userRoles' => $userRoles,
            'userRoles' => 'webmaster',
            'isAdminMenu' => true,
        );
        view()->share('CMSMenuHtml', $menu->getNavMenu());
    }
}