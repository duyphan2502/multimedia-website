<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Http\Request;

use App\Models;

use App\Models\Category;
use App\Models\CategoryContent;

use Illuminate\Support\Facades\Auth;

class AdminCategoryController extends BaseAdminController
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
        $fields = $request->except(['page', 'per_page']);

        $getByFields = [];
        if(isset($fields['global_title']))
        {
            $getByFields['global_title'] = ['compare' => 'LIKE', 'value' => $fields['global_title']];
        }

        $categories = Category::searchBy($getByFields, ['created_at' => 'desc'], true, $request->get('per_page', 10));
        $this->data = [
            'error' => false,
            'response_code' => 200,
            'data' => $categories->toArray()
        ];
        return response()->json($this->data, $this->data['response_code']);
    }

    public function getDetails(Request $request, $id, $language)
    {
        $this->data = [
            'error' => false,
            'response_code' => 200
        ];
        if(!$id == 0)
        {
            $category = Category::find($id);
            if(!$category)
            {
                $this->data = [
                    'error' => true,
                    'response_code' => 404,
                    'message' => 'The category you have tried to edit not found.'
                ];
                return response()->json($this->data, $this->data['response_code']);
            }
            $category = Category::getCategoryById($id, $language);

            /*Create new if not exists*/
            if(!$category)
            {
                $category = new CategoryContent();
                $category->language_id = $language;
                $category->category_id = $id;
                $category->save();

                $category = Category::getCategoryById($id, $language);
            }
            $this->data['data'] = $category->toArray();
        }
        /*If id == 0 ==> create category.*/

        return response()->json($this->data, $this->data['response_code']);
    }

    public function postEditGlobal(Request $request, Category $category, $id = null)
    {
        if($request->get('is_group_action') == true)
        {
            return $this->_GroupAction($request, $category);
        }
        $data = $request->except(['is_group_action', '_group_action', 'ids']);
        /*Just update some fields, not create new*/
        $result = $category->updateCategory($id, $data, true);
        return response()->json($result, $result['response_code']);
    }

    public function postEdit(Request $request, Category $category, $id, $language)
    {
        $data = $request->all();
        if($id == 0)
        {
            $result = $category->createCategory($id, $language, $data);
        }
        else
        {
            $result = $category->updateCategoryContent($id, $language, $data);
        }
        return response()->json($result, $result['response_code']);
    }

    public function deleteDelete(Request $request, Category $category, $id)
    {
        $result = $category->deleteCategory($id);
        return response()->json($result, $result['response_code']);
    }

    public function _GroupAction($request, $category)
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
        $this->data = $category->updateCategory($ids, $data, true);
        return response()->json($this->data, $this->data['response_code']);
    }
}