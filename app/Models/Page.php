<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

class Page extends AbstractModel
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
    protected $table = 'pages';

    protected $primaryKey = 'id';

    protected $rules = [
        'global_title' => 'required|max:255',
        'status' => 'integer|required'
    ];

    private $acceptableEdit = [
        'global_title',
        'status'
    ];

    protected $rulesEditContent = [
        'title' => 'required|max:255',
        'slug' => 'required|max:255|unique:page_contents',
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

    public function pageContent()
    {
        return $this->hasMany('App\Models\PageContent', 'page_id');
    }

    public static function getPageById($id, $languageId = 0)
    {
        return static::join('page_contents', 'pages.id', '=', 'page_contents.page_id')
            ->join('languages', 'languages.id', '=', 'page_contents.language_id')
            ->where('pages.id', '=', $id)
            ->where('page_contents.language_id', '=', $languageId)
            ->select('pages.global_title', 'page_contents.*')
            ->first();
    }

    public static function getPageContentByPageId($id, $languageId = 0)
    {
        return Models\PageContent::getBy([
            'page_id' => $id,
            'language_id' => $languageId
        ]);
    }

    public function updatePage($id, $data, $justUpdateSomeFields = false)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        if($id == 0)
        {
            $page = new static;
        }
        else
        {
            $page = static::find($id);
            if(!$page) return $result;
        }

        $validate = $this->validateData($data, null, null, $justUpdateSomeFields);
        if(!$validate && !$this->checkValueNotChange($page, $data))
        {
            return $this->getErrorsWithResponse();
        }

        foreach($data as $key => $row)
        {
            if(in_array($key, $this->acceptableEdit))
            {
                $page->$key = $row;
            }
        }

        if($page->save())
        {
            if($id == 0) $result['page_id'] = $page->id;
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = 'Update page completed!';
        }
        return $result;
    }

    public function updatePages($ids, $data, $justUpdateSomeFields = false)
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

        $pages = static::whereIn('id', $ids);
        if($pages->update($data))
        {
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = [
                'Update pages completed!'
            ];
        }

        return $result;
    }

    public function updatePageContent($id, $languageId, $data)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];

        $page = static::find($id);
        if(!$page)
        {
            $result['message'] = 'The page you have tried to edit not found.';
            $result['response_code'] = 404;
            return $result;
        }

        /*Update page content*/
        $pageContent = static::getPageContentByPageId($id, $languageId);
        if(!$pageContent)
        {
            $pageContent = new PageContent();
            $pageContent->language_id = $languageId;
            $pageContent->page_id = $id;
            $pageContent->save();

            //$pageContent = static::getPageContentByPageId($id, $languageId);
        }

        $validate = $this->validateData($data, $this->rulesEditContent);

        if(!$validate && !$this->checkValueNotChange($pageContent, $data))
        {
            return $this->getErrorsWithResponse();
        }

        foreach($data as $keyContent => $rowContent)
        {
            if(in_array($keyContent, $this->acceptableEditContent))
            {
                $pageContent->$keyContent = $rowContent;

                if($keyContent == 'slug')
                {
                    $pageContent->$keyContent = str_slug($rowContent);
                }
            }
        }
        if($pageContent->save())
        {
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = 'Update page completed!';
        }
        return $result;
    }

    public static function deletePage($id)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        $page = static::find($id);

        if(!$page)
        {
            $result['message'] = 'The page you have tried to edit not found';
            return $result;
        }

        $temp = PageContent::where('page_id', '=', $id);
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
                $result['message'] = ['Delete related content completed!'];
            }
            if($page->delete())
            {
                $result['error'] = false;
                $result['response_code'] = 200;
                $result['message'] = ['Delete page completed!'];
            }
        }
        else
        {
            if($page->delete())
            {
                $result['error'] = false;
                $result['response_code'] = 200;
            }
        }

        return $result;
    }

    public function createPage($id, $language, $data)
    {
        $dataPage = ['status' => 1];
        if(isset($data['title'])) $dataPage['global_title'] = $data['title'];
        if(!isset($data['status'])) $data['status'] = 1;
        if(!isset($data['language_id'])) $data['language_id'] = $language;

        $resultCreatePage = $this->updatePage($id, $dataPage);

        /*No error*/
        if(!$resultCreatePage['error'])
        {
            $page_id = $resultCreatePage['page_id'];
            $resultUpdatePageContent = $this->updatePageContent($page_id, $language, $data);
            $resultUpdatePageContent['page_id'] = $page_id;
            return $resultUpdatePageContent;
        }
        return $resultCreatePage;
    }
}