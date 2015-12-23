<?php namespace Acme;

use App\Models;
use App\Models\Language;

class CmsMenu
{
    /**
     * Construct
     */
    public function __construct()
    {
        $appLocale = app()->getLocale();
        $this->localeObj = Language::getBy(['default_locale' => $appLocale]);
        $this->languageCode = $this->localeObj->language_code;
    }

    var $args = array(
        'menuName' => '',
        'menuClass' => '',
        'container' => '',
        'containerClass' => '',
        'containerId' => '',
        'containerTag' => 'ul',
        'childTag' => 'li',
        'itemHasChildrenClass' => '',
        'subMenuClass' => 'sub-menu',
        'menuActive' => [
            'type' => 'customLink',
            'related_id' => 0
        ],
        'activeClass' => 'active',
        'userRoles' => array(),
        'isAdminMenu' => false,
    );

    public function getNavMenu()
    {
        $defaultArgs = array(
            'menuName' => '',
            'menuClass' => 'my-menu',
            'container' => 'nav',
            'containerClass' => '',
            'containerId' => '',
            'containerTag' => 'ul',
            'childTag' => 'li',
            'itemHasChildrenClass' => 'menu-item-has-children',
            'subMenuClass' => 'sub-menu',
            'menuActive' => [
                'type' => 'customLink',
                'related_id' => 0
            ],
            'activeClass' => 'active',
            'userRoles' => array(),
            'isAdminMenu' => false,
        );
        $defaultArgs = array_merge($defaultArgs, $this->args);

        $output = '';
        $menu = Models\Menu::getBy(['slug' => ltrim($defaultArgs['menuName'])]);
        // Menu exists
        if (!is_null($menu)) {
            if ($defaultArgs['container'] != '') $output .= '<' . $defaultArgs['container'] . ' class="' . $defaultArgs['containerClass'] . '" id="' . $defaultArgs['containerId'] . '">'; //<nav>
            $output .= '<' . $defaultArgs['containerTag'] . ' class="' . $defaultArgs['menuClass'] . '"' . (($defaultArgs['isAdminMenu']) ? ' data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200"' : '') . '>'; //<ul>
            $child_args = array(
                'menuId' => $menu->id,
                'parentId' => 0,
                'isAdminMenu' => false,
                'containerTag' => $defaultArgs['containerTag'],
                'childTag' => $defaultArgs['childTag'],
                'itemHasChildrenClass' => $defaultArgs['itemHasChildrenClass'],
                'subMenuClass' => $defaultArgs['subMenuClass'],
                'containerTagAttr' => '',
                'menuActive' => $defaultArgs['menuActive'],
                'defaultActiveClass' => $defaultArgs['activeClass'], //default active class
                'userRoles' => $defaultArgs['userRoles'], //check roles
            );
            if ($defaultArgs['isAdminMenu'] == true) {
                $child_args['isAdminMenu'] = true;
                $output .= '<li class="sidebar-toggler-wrapper">
										<div class="sidebar-toggler">
										</div>
									</li>';
            }
            $output .= $this->getMenuItems($child_args);
            // $output.= '<div class="clearfix"></div></'.$defaultArgs['containerTag'].'>'; //</ul>
            $output .= '</' . $defaultArgs['containerTag'] . '>'; //</ul>
            if ($defaultArgs['container'] != '') $output .= '</' . $defaultArgs['container'] . '>'; //</nav>
        }
        return $output;
    }

    // Function get all menu items
    private function getMenuItems($item_args)
    {
        $output = '';
        $menuItems = Models\MenuNode::getBy([
            'menu_id' => $item_args['menuId'],
            'parent_id' => $item_args['parentId'],
        ], ['position' => 'ASC'], true);

        if($menuItems)
        {
            (sizeof($menuItems) > 0 && $item_args['parentId'] != 0) ? $output .= '<' . $item_args['containerTag'] . ' class="' . $item_args['subMenuClass'] . '"' . $item_args['containerTagAttr'] . '>' : $output .= ''; // <ul> will be printed if current is not level 0
            foreach ($menuItems as $key => $row)
            {
                $arrow = '';
                if (count($row->child) > 0)
                {
                    $arrow = '<span class="arrow"></span>';
                }

                // Get menu active class
                $active_args = array(
                    'menuActive' => $item_args['menuActive'],
                    'item' => $row,
                    'defaultActiveClass' => $item_args['defaultActiveClass'],
                    'isAdminMenu' => $item_args['isAdminMenu'],
                );
                $activeClass = $this->getActiveItems($active_args);
                if ($this->checkChildItemIsActive(array('parent' => $row, 'menuActive' => $item_args['menuActive'], 'defaultActiveClass' => $item_args['defaultActiveClass'], 'isAdminMenu' => $item_args['isAdminMenu'])) == true)
                {
                    $activeClass = 'active';
                    $arrow = '<span class="arrow open"></span>';
                }

                $menu_title = $this->getMenuItemTitle($row);
                $menu_link = $this->getMenuItemLink($row, $item_args['isAdminMenu']);
                $parent_class = $row->css_class . ' ';
                if ($this->checkItemHasChildren($row))
                {
                    $parent_class .= $item_args['itemHasChildrenClass'];
                }

                $child_args = array(
                    'menuId' => $item_args['menuId'],
                    'parentId' => $row->id,
                    'isAdminMenu' => $item_args['isAdminMenu'],
                    'containerTag' => $item_args['containerTag'],
                    'childTag' => $item_args['childTag'],
                    'itemHasChildrenClass' => $item_args['itemHasChildrenClass'],
                    'subMenuClass' => $item_args['subMenuClass'],
                    'containerTagAttr' => '',
                    'menuActive' => $item_args['menuActive'],
                    'defaultActiveClass' => $item_args['defaultActiveClass'], //default active class
                    'userRoles' => $item_args['userRoles'], //check roles
                );

                $menu_icon = $menu_title;
                $linkClass = '';
                if ($item_args['isAdminMenu'] == true)
                {
                    $linkClass = ' nav-link ';
                    $activeClass .= ' nav-item ';
                    $menu_icon = '<i class="' . $row->icon_font . '"></i> <span class="title">' . $menu_title . '</span><span class="selected"></span>' . $arrow;
                }
                if ($item_args['isAdminMenu'] != true || $item_args['userRoles'] == 'webmaster' || in_array($row->id, $item_args['userRoles']))
                {
                    $output .= '<' . $item_args['childTag'] . ' class="' . $parent_class . ' ' . $activeClass . '">'; #<li>
                    //$output .= '<a href="'.$menu_link.'" title="'.$menu_title.'">'.$menu_icon.$arrow.'</a>';
                    $output .= '<a class="'.$linkClass.'" href="' . $menu_link . '" title="' . $menu_title . '">' . $menu_icon . '</a>';
                    $output .= $this->getMenuItems($child_args);
                    $output .= '</' . $item_args['childTag'] . '>'; #</li>
                }
            }
            (sizeof($menuItems) > 0 && $item_args['parentId'] != 0) ? $output .= '</' . $item_args['containerTag'] . '>' : $output .= ''; // </ul>
        }
        return $output;
    }

    // Menu active
    private function getActiveItems($args)
    {
        $temp = $args['menuActive'];
        $result = '';
        if ($args['item']->type == $args['menuActive']['type']) {
            switch ($args['menuActive']['type']) {
                case 'category': {
                    if ($args['menuActive']['related_id'] == $args['item']->related_id) {
                        $result = $args['defaultActiveClass'];
                    }
                }
                    break;
                case 'productCategory': {
                    if ($args['menuActive']['related_id'] == $args['item']->related_id) {
                        $result = $args['defaultActiveClass'];
                    }
                }
                    break;
                case 'customLink': {
                    $currentUrl = \Request::url();
                    if ($args['isAdminMenu']) {
                        if ($args['menuActive']['related_id'] == $args['item']->url) {
                            $result = $args['defaultActiveClass'];
                        }
                    } else {
                        if ($args['item']->url == $currentUrl || $args['item']->url == $currentUrl . '/') {
                            $result = $args['defaultActiveClass'];
                        }
                    }
                }
                    break;
                default: {
                    if ($args['menuActive']['related_id'] == $args['item']->related_id) {
                        $result = $args['defaultActiveClass'];
                    }
                }
                    break;
            }
        }
        return $result;
    }

    // Check children active
    private function checkChildItemIsActive($args)
    {
        foreach ($args['parent']->child as $key => $row) {
            if ($this->getActiveItems(array('menuActive' => $args['menuActive'], 'item' => $row, 'defaultActiveClass' => $args['defaultActiveClass'], 'isAdminMenu' => $args['isAdminMenu'])) != '') {
                return true;
            }
        }
        return false;
    }

    // Get item title
    private function getMenuItemTitle($item)
    {
        $data_title = '';
        switch ($item->type) {
            case 'page': {
                $title = $item->title;
                if (!$title) {
                    $page = Models\Page::getBy([
                        'id' => $item->related_id
                    ]);
                    if ($page) {
                        $pageContent = $page->pageContent()->join('languages', 'languages.id', '=', 'page_contents.language_id')
                            ->where('languages.id', '=', $this->localeObj->id)
                            ->select('page_contents.title')
                            ->first();
                        if ($pageContent) {
                            $title = ((trim($pageContent->title) != '') ? trim($pageContent->title) : trim($page->global_title));
                        }
                    } else {
                        $title = '';
                    }
                }
                $data_title = $title;
            }
                break;
            case 'category': {
                $title = $item->title;
                if (!$title) {
                    $cat = Models\Category::getBy([
                        'id' => $item->related_id
                    ]);
                    if ($cat) {
                        $categoryContent = $cat->categoryContent()->join('languages', 'languages.id', '=', 'category_contents.language_id')
                            ->where('languages.id', '=', $this->localeObj->id)
                            ->select('category_contents.title')
                            ->first();
                        if ($categoryContent) {
                            $title = ((trim($categoryContent->title) != '') ? trim($categoryContent->title) : trim($cat->global_title));
                        }
                    } else {
                        $title = '';
                    }
                }
                $data_title = $title;
            }
                break;
            case 'productCategory': {
                $title = $item->title;
                if (!$title) {
                    $cat = Models\ProductCategory::getBy([
                        'id' => $item->related_id
                    ]);
                    if ($cat) {
                        $categoryContent = $cat->categoryContent()->join('languages', 'languages.id', '=', 'productcat_contents.language_id')
                            ->where('languages.id', '=', $this->localeObj->id)
                            ->select('productcat_contents.title')
                            ->first();
                        if ($categoryContent) {
                            $title = ((trim($categoryContent->title) != '') ? trim($categoryContent->title) : trim($cat->global_title));
                        }
                    } else {
                        $title = '';
                    }
                }
                $data_title = $title;
            }
                break;
            case 'customLink': {
                $data_title = $item->title;
                if (!$data_title) $data_title = '';
            }
                break;
            default: {
                $data_title = $item->title;
                if (!$data_title) $data_title = '';
            }
                break;
        }
        $data_title = htmlentities($data_title);
        return $data_title;
    }

    // Get item links
    private function getMenuItemLink($item, $isAdminMenu = false)
    {
        $result = '';
        switch ($item->type) {
            case 'page': {
                $page = Models\Page::getBy([
                    'id' => $item->related_id
                ]);
                if ($page) {
                    $pageContent = $page->pageContent()->join('languages', 'languages.id', '=', 'page_contents.language_id')
                        ->where('languages.id', '=', $this->localeObj->id)
                        ->select('page_contents.slug')
                        ->first();
                    if ($pageContent) {
                        $slug = (trim($pageContent->slug) != '') ? trim($pageContent->slug) : '';
                    }
                } else {
                    $slug = '';
                }

                $result = asset($this->languageCode . '/' . $slug);
            }
                break;
            case 'category': {

            }
                break;
            case 'productCategory': {

            }
                break;
            case 'customLink': {
                if ($isAdminMenu == true) {
                    $result = asset(\Config::get('app.adminCpAccess') . '/' . $item->url);
                } else {
                    $result = $item->url;
                }
            }
                break;
            default: {
                if ($isAdminMenu == true) {
                    $result = asset(\Config::get('app.adminCpAccess') . '/' . $item->url);
                } else {
                    $result = $item->url;
                }
            }
                break;
        }
        return $result;
    }

    // Check menu has children or not
    private function checkItemHasChildren($item)
    {
        if (count($item->child) > 0) return true;
        return false;
    }

    /*Get parent slug*/
    private function getParentProductCategorySlugs($parentId, $currentSlug)
    {
        $result = $currentSlug;
        $category = ProductCategoryContent::join('productcats', 'productcats.id', '=', 'productcat_contents.productcat_id')
            ->join('languages', 'languages.id', '=', 'productcat_contents.language_id')
            ->where('languages.id', '=', $this->localeObj->id)
            ->where('productcats.id', '=', $parentId)
            ->select('productcat_contents.*', 'productcats.global_slug', 'productcats.parent_id')
            ->first();
        if ($category) {
            $categorySlug = ($category->slug) ? $category->slug : $category->global_slug;
            if ($categorySlug) {
                $result = $categorySlug . '/' . $result;
            }
            if ($category->productcat_id != 0) $result = $this->getParentProductCategorySlugs($category->parent_id, $result);
        }
        return $result;
    }

    private function getParentCategorySlugs($parentId, $currentSlug)
    {
        $result = $currentSlug;
        $category = CategoryContent::join('categories', 'categories.id', '=', 'category_contents.category_id')
            ->join('languages', 'languages.id', '=', 'category_contents.language_id')
            ->where('languages.id', '=', $this->localeObj->id)
            ->where('categories.id', '=', $parentId)
            ->select('category_contents.*', 'categories.global_slug', 'categories.parent_id')
            ->first();
        if ($category) {
            $categorySlug = ($category->slug) ? $category->slug : $category->global_slug;
            if ($categorySlug) {
                $result = $categorySlug . '/' . $result;
            }
            if ($category->category_id != 0) $result = $this->getParentCategorySlugs($category->parent_id, $result);
        }
        return $result;
    }
}