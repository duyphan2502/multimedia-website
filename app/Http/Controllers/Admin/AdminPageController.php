<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

use App\Models\Page;
use App\Models\PageContent;

use Illuminate\Pagination\Paginator;

class AdminPageController extends BaseAdminController
{
    var $bodyClass = 'page-controller';
    public function __construct()
    {
        parent::__construct();

        $this->_setPageTitle('Pages', 'manage static pages.');
        $this->_setBodyClass($this->bodyClass);

        $this->loadAdminMenu('pages');
    }

    public function getIndex(Request $request)
    {
        $this->_setBodyClass($this->bodyClass.' pages-list-page');
        return $this->viewAdmin('pages.index');
    }

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

        $items = Page::searchBy($getByFields, [$orderBy => $orderType], true, $limit);

        $iTotalRecords = $items->total();
        $sEcho = intval($request->get('sEcho'));

        foreach ($items as $key => $row)
        {
            $status = '<span class="label label-success label-sm">Activated</span>';
            if($row->status != 1)
            {
                $status = '<span class="label label-danger label-sm">Disabled</span>';
            }
            $records["data"][] = array(
                '<input type="checkbox" name="id[]" value="'.$row->id.'">',
                $row->id,
                $row->global_title,
                $status,
                $row->created_at->toDateTimeString(),
                ''
            );
        }

        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $iTotalRecords;
        $records["iTotalDisplayRecords"] = $iTotalRecords;

        return response()->json($records);
    }

    public function getDetails(Request $request, $id, $language)
    {
        $this->data = [
            'error' => false,
            'response_code' => 200
        ];
        if(!$id == 0)
        {
            $page = Page::find($id);
            if(!$page)
            {
                $this->data = [
                    'error' => true,
                    'response_code' => 404,
                    'message' => 'The page you have tried to edit not found.'
                ];
                return response()->json($this->data, $this->data['response_code']);
            }
            $page = Page::getPageById($id, $language);

            /*Create new if not exists*/
            if(!$page)
            {
                $page = new PageContent();
                $page->language_id = $language;
                $page->page_id = $id;
                $page->save();
                $page = Page::getPageById($id, $language);
            }
            $this->data['data'] = $page->toArray();
        }
        /*If id == 0 ==> create page.*/

        return response()->json($this->data, $this->data['response_code']);
    }

    public function postEditGlobal(Request $request, Page $page, $id = null)
    {
        if($request->get('is_group_action') == true)
        {
            return $this->_GroupAction($request, $page);
        }
        $data = $request->except(['is_group_action', 'group_action', 'ids']);
        /*Just update some fields, not create new*/
        $result = $page->updatePage($id, $data, true);
        return response()->json($result, $result['response_code']);
    }

    public function postEdit(Request $request, Page $page, $id, $language)
    {
        $data = $request->all();
        if(!$data['slug'])
        {
            $data['slug'] = str_slug($data['title']);
        }

        if($id == 0)
        {
            $result = $page->createPage($id, $language, $data);
        }
        else
        {
            $result = $page->updatePageContent($id, $language, $data);
        }
        return response()->json($result, $result['response_code']);
    }

    public function deleteDelete(Request $request, Page $page, $id)
    {
        $result = $page->deletePage($id);
        return response()->json($result, $result['response_code']);
    }

    public function _GroupAction($request, $page)
    {
        $this->data['message'] = 'Some error occurred!';


        $ids = $request->get('ids');

        if(!$ids) return response()->json($this->data, $this->data['response_code']);

        $data = [];
        switch($request->get('group_action'))
        {
            case 'disable':
            {
                $data['status'] = 0;
            } break;
            case 'active':
            {
                $data['status'] = 1;
            } break;
            default:
            {
                /*No action*/
                $this->data['message'] = 'Not allowed task.';
                return response()->json($this->data, $this->data['response_code']);
            } break;
        }

        /*Just update some fields, not create new*/
        $this->data = $page->updatePages($ids, $data, true);
        return response()->json($this->data, $this->data['response_code']);
    }
}