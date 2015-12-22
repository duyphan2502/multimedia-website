<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

use App\Models\Menu;
use App\Models\MenuNode;

use Illuminate\Pagination\Paginator;

class AdminMenuController extends BaseAdminController
{
    var $bodyClass = 'menu-controller';
    public function __construct()
    {
        parent::__construct();

        $this->_setPageTitle('Menus', 'manage menus.');
        $this->_setBodyClass($this->bodyClass);

        $this->loadAdminMenu('menus');
    }

    public function getIndex(Request $request)
    {
        $this->_setBodyClass($this->bodyClass.' menus-list-page');
        return $this->viewAdmin('menus.index');
    }

    /*Use for plugin Datatables*/
    public function postIndex(Request $request)
    {
        /**
         * Paging
         **/
        $offset = $request->get('start', 0);
        $limit = $request->get('length', 10);
        $paged = ($offset + $limit) / $limit;
        Paginator::currentPageResolver(function() use ($paged) {
            return $paged;
        });

        $records = [];
        $records["data"] = [];

        /*
        * Sortable data
        */
        $orderBy = $request->get('order')[0]['column'];
        switch ($orderBy) {
            case 1:
            {
                $orderBy = 'id';
            }
                break;
            case 2:
            {
                $orderBy = 'global_title';
            }
                break;
            default:
            {
                $orderBy = 'created_at';
            }
                break;
        }
        $orderType = $request->get('order')[0]['dir'];

        $getByFields = [];
        if($request->get('global_title', null) != null)
        {
            $getByFields['global_title'] = ['compare' => 'LIKE', 'value' => $request->get('global_title')];
        }
        if($request->get('status', null) != null)
        {
            $getByFields['status'] = ['compare' => '=', 'value' => $request->get('status')];
        }

        $items = Menu::searchBy($getByFields, [$orderBy => $orderType], true, $limit);

        $iTotalRecords = $items->total();
        $sEcho = intval($request->get('sEcho'));

        foreach ($items as $key => $row)
        {
            /*Status*/
            $status = '<span class="label label-success label-sm">Activated</span>';
            if($row->status != 1)
            {
                $status = '<span class="label label-danger label-sm">Disabled</span>';
            }
            /*Edit link*/
            $link = asset($this->adminCpAccess.'/menus/edit/'.$row->id);

            $records["data"][] = array(
                '<input type="checkbox" name="id[]" value="'.$row->id.'">',
                $row->id,
                $row->title,
                $row->slug,
                $status,
                $row->created_at->toDateTimeString(),
                '<a href="'.$link.'" class="btn btn-outline green btn-sm"><i class="icon-pencil"></i></a>'
            );
        }

        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $iTotalRecords;
        $records["iTotalDisplayRecords"] = $iTotalRecords;

        return response()->json($records);
    }

    public function getEdit(Request $request, $id)
    {
        $menu = Menu::find($id);
        if(!$menu) return $this->_showErrorPage(404, 'Menu not found');

        $this->_setPageTitle('Menus', $menu->title);

        $dis['object'] = $menu;

        $dis['nestableMenuSrc'] = $this->_getNestableMenuSrc($menu, 0);

        return $this->viewAdmin('menus.edit', $dis);
    }

    public function postEdit(Request $request, $id)
    {
        $menu = Menu::find($id);
        if(!$menu) return $this->_showErrorPage(404, 'Menu not found');

        $data = $request->only(['title', 'slug']);

        $data['slug'] = ($data['slug']) ? str_slug($data['slug']) : str_slug($data['title']);

        $result = $menu->updateMenu($id, $data, true);

        if($result['error'])
        {
            $this->_setFlashMessage($result['message'], 'error');
            $this->_showFlashMessages();

            return redirect()->back();
        }
        $this->_setFlashMessage($result['message'], 'success');

        $json = json_decode($request->get('menu_nodes', null));
        if($json)
        {
            $this->_recursiveSaveMenu($json, $menu->id, 0);
        }

        $this->_showFlashMessages();
        return redirect()->back();
    }

    private function _getMenuNodes($menu_id, $parent_id)
    {
        $menu_nodes = MenuNode::getBy([
            'menu_id' => $menu_id,
            'parent_id' => $parent_id
        ], ['position' => 'ASC'], true);
        return $menu_nodes;
    }

    private function _getNestableMenuSrc($menu, $parent_id)
    {
        $menu_nodes = $this->_getMenuNodes($menu->id, $parent_id);
        $html_src = '';
        $html_src .= '<ol class="dd-list">';
        foreach ($menu_nodes as $key => $row) :
            $data_title = $row->title;
            if(!$data_title || $data_title == '' || trim($data_title, '') == '')
            {
                switch($row->type) {
                    case 'category':
                    {
                        $category = $row->category;
                        if($category) $data_title = $category->global_title;
                    } break;
                    case 'productCategory':
                    {
                        $category = $row->productCategory;
                        if($category) $data_title = $category->global_title;
                    } break;
                    case 'page':
                    {
                        $post = $row->post;
                        if($post) $data_title = $post->global_title;
                    } break;
                    default:
                    {
                        $post = $row->post;
                        if($post) $data_title = $post->global_title;
                    } break;
                }
            }
            $data_title = htmlentities($data_title);

            $html_src .= '<li class="dd-item dd3-item '.(($row->post_id > 0 && $row->post_id != '' && $row->post_id != null) ? 'post-item' : '').'" data-type="'.$row->type.'" data-catid="'.$row->category_id.'" data-postid="'.$row->post_id.'" data-title="'.$row->title.'" data-class="'.$row->css_class.'" data-id="'.$row->id.'" data-customurl="'.$row->url.'" data-iconfont="'.$row->icon_font.'">';
            $html_src .= '<div class="dd-handle dd3-handle"></div>';
            $html_src .= '<div class="dd3-content">';
            $html_src .= '<span class="text pull-left" data-update="title">'.$data_title.'</span>';
            $type = $row->type;
            if($type == null || $type == '' || trim($type, '') == '')
            {
                $type = $row->post->get()->post_type;
            }
            $html_src .= '<span class="text pull-right">'.$type.'</span>';
            $html_src .= '<a href="#" title="" class="show-item-details"><i class="fa fa-angle-down"></i></a>';
            $html_src .= '<div class="clearfix"></div>';
            $html_src .= '</div>';
            $html_src .= '<div class="item-details">';
            $html_src .= '<label class="pad-bot-5">';
            $html_src .= '<span class="text pad-top-5 dis-inline-block" data-update="title">Title</span>';
            $html_src .= '<input type="text" name="title" value="'.htmlentities($row->title).'" data-old="'.htmlentities($row->title).'" >';
            $html_src .= '</label>';
            if($row->post_id > 0 && $row->post_id != '' && $row->post_id != null || $row->category_id > 0) {} else
            {
                $html_src .= '<label class="pad-bot-5 dis-inline-block">';
                $html_src .= '<span class="text pad-top-5" data-update="customurl">Url</span>';
                $html_src .= '<input type="text" name="customurl" value="'.$row->url.'" data-old="'.$row->url.'">';
                $html_src .= '</label>';
            }
            /*Icon font*/
            $html_src .= '<label class="pad-bot-5 dis-inline-block">';
            $html_src .= '<span class="text pad-top-5" data-update="iconfont">Icon - font</span>';
            $html_src .= '<input type="text" name="iconfont" value="'.$row->icon_font.'" data-old="'.$row->icon_font.'">';
            $html_src .= '</label>';
            /*Icon font*/

            $html_src .= '<label class="pad-bot-10">';
            $html_src .= '<span class="text pad-top-5 dis-inline-block">CSS class</span>';
            $html_src .= '<input type="text" name="class" value="'.$row->css_class.'" data-old="'.$row->css_class.'">';
            $html_src .= '</label>';
            $html_src .= '<div class="text-right">';
            $html_src .= '<a href="#" title="" class="btn red btn-remove btn-sm">Remove</a>';
            $html_src .= '<a href="#" title="" class="btn blue btn-cancel btn-sm">Cancel</a>';
            $html_src .= '</div>';
            $html_src .= '</div>';
            $html_src .= '<div class="clearfix"></div>';
            $html_src .= $this->_getNestableMenuSrc($menu, $row->id);
            $html_src .= '</li>';
        endforeach;
        $html_src .= '</ol>';
        return $html_src;
    }

    private function _saveMenuNode($json_item, $menu_id, $parent_id)
    {
        if($json_item->id && $json_item->id > 0)
        {
            $item = MenuNode::findOrNew($json_item->id);
        }
        else
        {
            $item = new MenuNode();
        }

        $item->title = (isset($json_item->title)) ? $json_item->title : '';
        $item->url = (isset($json_item->customurl)) ? $json_item->customurl : '';
        $item->css_class = (isset($json_item->class)) ? $json_item->class : '';
        $item->position = (isset($json_item->position)) ? $json_item->position : '';
        $item->icon_font = (isset($json_item->iconfont)) ? $json_item->iconfont : '';
        $item->type = (isset($json_item->type)) ? $json_item->type : '';
        $item->menu_id = $menu_id;
        $item->parent_id = $parent_id;

        switch($json_item->type)
        {
            case 'customLink':
            {
                $item->related_id = 0;
            } break;
            case 'page':
            {
                $item->related_id = $json_item->postid;
            } break;
            case 'category':
            {
                $item->related_id = $json_item->catid;
            } break;
            case 'productCategory':
            {
                $item->related_id = $json_item->catid;
            } break;
        }

        if(!$item->save())
        {
            $this->_setFlashMessage('Some error occurred when update item - '.$item->title, 'error');
            return null;
        }
        return $item->id;
    }

    private function _recursiveSaveMenu($json_arr, $menu_id, $parent_id)
    {
        foreach($json_arr as $key => $row)
        {
            $parent = $this->_saveMenuNode($row, $menu_id, $parent_id);
            if($parent != null)
            {
                if(!empty($row->children))
                {
                    $this->_recursiveSaveMenu($row->children, $menu_id, $parent);
                }
            }
        }
    }
}