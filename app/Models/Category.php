<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

class Category extends AbstractModel
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categories';

    protected $primaryKey = 'id';

    protected $rules = [
        'global_title' => 'required|max:255',
        'status' => 'integer|required'
    ];

    private $acceptableEdit = [
        'global_title',
        'status',
        'parent_id'
    ];

    protected $rulesEditContent = [
        'title' => 'required|max:255',
        'slug' => 'required|max:255|unique:category_contents',
        'language_id' => 'min:1|integer|required',
        'description' => 'max:1000',
        'content' => 'max:5000|string',
        'status' => 'integer|required',
        'thumbnail' => 'string|max:255',
        'tags' => 'string|max:255',
    ];

    private $acceptableEditContent = [
        'title',
        'slug',
        'language_id',
        'description',
        'content',
        'status',
        'thumbnail',
        'tags'
    ];

    public static function getCategoryById($id, $languageId = 0)
    {
        return static::join('category_contents', 'categories.id', '=', 'category_contents.category_id')
            ->join('languages', 'languages.id', '=', 'category_contents.language_id')
            ->where('categories.id', '=', $id)
            ->where('category_contents.language_id', '=', $languageId)
            ->select('categories.global_title', 'categories.parent_id', 'category_contents.*')
            ->first();
    }

    public static function getCategoryContentByCategoryId($id, $languageId = 0)
    {
        return Models\CategoryContent::getBy([
            'category_id' => $id,
            'language_id' => $languageId
        ]);
    }

    public function updateCategory($id, $data, $justUpdateSomeFields = false)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        if($id == 0)
        {
            $category = new static;
        }
        else
        {
            $category = static::find($id);
            if(!$category) return $result;
        }

        $validate = $this->validateData($data, null, null, $justUpdateSomeFields);
        if(!$validate && !$this->checkValueNotChange($category, $data))
        {
            return $this->getErrorsWithResponse();
        }

        foreach($data as $key => $row)
        {
            if(in_array($key, $this->acceptableEdit))
            {
                $category->$key = $row;
            }
        }

        if($category->save())
        {
            if($id == 0) $result['category_id'] = $category->id;
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = 'Update category completed!';
        }
        return $result;
    }

    public function updateCategories($ids, $data, $justUpdateSomeFields = false)
    {
        $validate = $this->validateData($data, null, null, $justUpdateSomeFields);
        if(!$validate)
        {
            return $this->getErrorsWithResponse();
        }

        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        foreach($data as $key => $row)
        {
            if(!in_array($key, $this->acceptableEdit))
            {
                unset($data[$key]);
            }
        }

        $categories = static::whereIn('id', $ids);
        if($categories->update($data))
        {
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = [
                'Update categories completed!'
            ];
        }

        return $result;
    }

    public function updateCategoryContent($id, $languageId, $data)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];

        $category = static::find($id);
        if(!$category)
        {
            $result['message'] = 'The category you have tried to edit not found.';
            $result['response_code'] = 404;
            return $result;
        }

        /*Update page content*/
        $categoryContent = static::getCategoryContentByCategoryId($id, $languageId);
        if(!$categoryContent)
        {
            $categoryContent = new CategoryContent();
            $categoryContent->language_id = $languageId;
            $categoryContent->category_id = $id;
            $categoryContent->save();
        }

        $validate = $this->validateData($data, $this->rulesEditContent);

        if(!$validate && !$this->checkValueNotChange($categoryContent, $data))
        {
            return $this->getErrorsWithResponse();
        }

        foreach($data as $keyContent => $rowContent)
        {
            if(in_array($keyContent, $this->acceptableEditContent))
            {
                $categoryContent->$keyContent = $rowContent;

                if($keyContent == 'slug')
                {
                    $categoryContent->$keyContent = str_slug($rowContent);
                }
            }
        }
        if($categoryContent->save())
        {
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = 'Update category completed!';
        }
        return $result;
    }

    public static function deleteCategory($id)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        $category = static::find($id);

        if(!$category)
        {
            $result['message'] = 'The category you have tried to edit not found';
            return $result;
        }

        $temp = CategoryContent::where('category_id', '=', $id);
        $related = $temp->get();
        if(!count($related))
        {
            $related = null;
        }

        /*Remove all related content*/
        if($related != null)
        {
            if($temp->delete())
            {
                $result['error'] = false;
                $result['response_code'] = 200;
                $result['messages'][] = 'Delete related content completed!';
            }
            if($category->delete())
            {
                $result['error'] = false;
                $result['response_code'] = 200;
                $result['messages'][] = 'Delete category completed!';
            }
        }
        else
        {
            if($category->delete())
            {
                $result['error'] = false;
                $result['response_code'] = 200;
                $result['messages'][] = 'Delete category completed!';
            }
        }

        return $result;
    }

    public function createCategory($id, $language, $data)
    {
        $dataCategory = ['status' => 1];
        if(isset($data['title'])) $dataCategory['global_title'] = $data['title'];
        if(!isset($data['status'])) $data['status'] = 1;
        if(!isset($data['language_id'])) $data['language_id'] = $language;

        $resultCreateCategory = $this->updateCategory($id, $dataCategory);

        /*No error*/
        if(!$resultCreateCategory['error'])
        {
            $category_id = $resultCreateCategory['category_id'];
            $resultUpdateCategoryContent = $this->updateCategoryContent($category_id, $language, $data);
            $resultUpdateCategoryContent['category_id'] = $category_id;
            return $resultUpdateCategoryContent;
        }
        return $resultCreateCategory;
    }
}