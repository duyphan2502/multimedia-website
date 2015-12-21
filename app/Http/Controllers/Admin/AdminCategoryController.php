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
    var $data = [
        'error' => true,
        'response_code' => 500
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getIndex(Request $request)
    {
        $fields = $request->except(['page', 'per_page']);

        $getByFields = [];
        if (isset($fields['global_title'])) {
            $getByFields['global_title'] = ['compare' => 'LIKE', 'value' => $fields['global_title']];
        }

        if(sizeof($getByFields) > 0)
        {
            $categories = Category::searchBy($getByFields, ['created_at' => 'desc'], true, $request->get('per_page', 0))->toArray();
        }
        else
        {
            $categories = $this->_recursiveGetCategories($request, 0);
            $categories = $this->_recursiveShowCategoriesWithSub($categories, 0);
        }

        $this->data = [
            'error' => false,
            'response_code' => 200,
            'data' => $categories
        ];
        return response()->json($this->data, $this->data['response_code']);
    }

    /*
     * Get all categories with children node.
     * @param $request: instance of Request
     * @param $parentId: parent_id.
     * @return array
     * */
    private function _recursiveGetCategories($request, $parentId)
    {
        $results = [];
        $getByFields = [
            'parent_id' => $parentId
        ];
        $categories = Category::getBy($getByFields, ['global_title' => 'asc'], true, 0);

        foreach ($categories as $key => $row)
        {
            $row->childrenCategories = $this->_recursiveGetCategories($request, $row->id);
            array_push($results, $row);
        }
        return $results;
    }

    /*
     * When get categories, auto add sub text to each element.
     * @param $categories: A list categories
     * @param $level: level of category
     * @param $subText: sub text
     * @return array
     * */
    private function _recursiveShowCategoriesWithSub($categories, $level = 0, $subText = '——')
    {
        $result = [];

        $childText = '';
        if($level > 0)
        {
            for ($i=0; $i < $level; $i++)
            {
                $childText .= $subText;
            }
        }
        foreach ($categories as $key => $row)
        {
            $data = [
                'id' => $row->id,
                'parent_id' => $row->parent_id,
                'global_title' => $row->global_title,
                'sub_title' => $childText,
                'status' => $row->status,
                'created_at' => $row->created_at->toDateTimeString(),
                'updated_at' => $row->updated_at->toDateTimeString()
            ];
            array_push($result, $data);
            if(sizeof($row->childrenCategories) > 0)
            {
                $childrenCategories = $this->_recursiveShowCategoriesWithSub($row->childrenCategories, $level + 1, $subText);
                foreach ($childrenCategories as $keyChild => $childRow)
                {
                    array_push($result, $childRow);
                }
            }
        }
        return $result;
    }

    public function getDetails(Request $request, $id, $language)
    {
        $this->data = [
            'error' => false,
            'response_code' => 200
        ];
        if (!$id == 0) {
            $category = Category::find($id);
            if (!$category) {
                $this->data = [
                    'error' => true,
                    'response_code' => 404,
                    'message' => 'The category you have tried to edit not found.'
                ];
                return response()->json($this->data, $this->data['response_code']);
            }
            $category = Category::getCategoryById($id, $language);

            /*Create new if not exists*/
            if (!$category) {
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
        if ($request->get('is_group_action') == true) {
            return $this->_GroupAction($request, $category);
        }
        $data = $request->except(['is_group_action', 'group_action', 'ids', 'sub_title']);
        /*Just update some fields, not create new*/
        $result = $category->updateCategory($id, $data, true);
        return response()->json($result, $result['response_code']);
    }

    public function postEdit(Request $request, Category $category, $id, $language)
    {
        $data = $request->except('parent_id');
        $slug = $request->get('slug', null);
        $title = $request->get('title', null);
        if(!$slug)
        {
            $data['slug'] = str_slug($title);
        }
        /*Update parent_id*/
        $parent_id = $request->get('parent_id', 0);
        if(!$parent_id || (int)$parent_id == (int)$id)
        {
            $dataGlobal = [
                'parent_id' => 0
            ];
        }
        else
        {
            $dataGlobal = [
                'parent_id' => $parent_id
            ];
        }
        $result = $category->updateCategory($id, $dataGlobal, true);
        if($result['error'] == true)
        {
            return response()->json($result, $result['response_code']);
        }

        if ($id == 0) {
            $result = $category->createCategory($id, $language, $data);
        } else {
            $result = $category->updateCategoryContent($id, $language, $data);
        }
        return response()->json($result, $result['response_code']);
    }

    public function deleteDelete(Request $request, Category $category, $id)
    {
        /*Delete base category*/
        $result = $category->deleteCategory($id);
        if(!$result['error'])
        {
            $result['message'] = 'Category deleted';
            /*Update child categories*/
            $relatedContent = Category::where('parent_id', $id)->get();
            $subCategories = [];
            foreach($relatedContent as $key => $row)
            {
                array_push($subCategories, $row->id);
            }
            $resultRelated = $category->updateCategories($subCategories, [
                'parent_id' => 0
            ], true);
            if($resultRelated['error'])
            {
                $resultRelated['message'] = 'Error when update related categories';
            }
            $result['messages'] = $resultRelated['message'];
        }

        return response()->json($result, $result['response_code']);
    }

    public function _GroupAction($request, $category)
    {
        $this->data['message'] = 'Some error occurred!';


        $ids = $request->get('ids');

        if (!$ids) return response()->json($this->data, $this->data['response_code']);

        $data = [];
        switch ($request->get('group_action')) {
            case 'disable': {
                $data['status'] = 0;
            }
                break;
            case 'active': {
                $data['status'] = 1;
            }
                break;
            default: {
                /*No action*/
                $this->data['message'] = 'Not allowed task.';
                return response()->json($this->data, $this->data['response_code']);
            }
                break;
        }

        /*Just update some fields, not create new*/
        $this->data = $category->updateCategories($ids, $data, true);
        return response()->json($this->data, $this->data['response_code']);
    }
}