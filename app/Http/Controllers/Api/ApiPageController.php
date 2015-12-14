<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Models;

use App\Models\Page;
use App\Models\PageContent;

class ApiPageController extends BaseController
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
        $getByFields = $request->except(['page', 'per_page']);
        $pages = Page::searchBy($getByFields, ['created_at' => 'desc'], true, $request->get('per_page', 10));
        $this->data = [
            'error' => false,
            'response_code' => 200,
            'data' => $pages->toArray()
        ];
        return response()->json($this->data, $this->data['response_code']);
    }

    public function getDetails(Request $request, $id, $language)
    {
        $this->data = [
            'error' => false,
            'response_code' => 200
        ];
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
        return response()->json($this->data, $this->data['response_code']);
    }

    public function postEditGlobal(Request $request, Page $page, $id = null)
    {
        if($request->get('is_group_action') == true)
        {
            return $this->_GroupAction($request, $page);
        }
        $data = $request->except(['is_group_action', '_group_action', 'ids']);
        /*Just update some fields, not create new*/
        $result = $page->updatePage($id, $data, true);
        return response()->json($result, $result['response_code']);
    }

    public function postEdit(Request $request, Page $page, $id, $language)
    {
        $data = $request->all();
        $result = $page->updatePageContent($id, $language, $data);
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
        switch($request->get('_group_action'))
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