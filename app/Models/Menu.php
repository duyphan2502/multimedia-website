<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

class Menu extends AbstractModel
{
    private $acceptableEdit = [
        'title',
        'slug',
        'is_admin_menu'
    ];
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menus';

    protected $primaryKey = 'id';

    /**
     * Validation
     */
    public $rules = array(
        'slug' => 'required|unique:menus',
    );

    public function menuNode()
    {
        return $this->hasMany('App\Models\MenuNode', 'menu_id');
    }

    public function updateMenu($id, $data, $justUpdateSomeFields = false)
    {
        $result = [
            'error' => true,
            'response_code' => 500,
            'message' => 'Some error occurred!'
        ];
        if($id == 0)
        {
            $object = new static;
        }
        else
        {
            $object = static::find($id);
            if(!$object) return $result;
        }

        $validate = $this->validateData($data, null, null, $justUpdateSomeFields);
        if(!$validate && !$this->checkValueNotChange($object, $data))
        {
            return $this->getErrorsWithResponse();
        }
        foreach($data as $key => $row)
        {
            if(in_array($key, $this->acceptableEdit))
            {
                $object->$key = $row;
            }
        }

        if($object->save())
        {
            if($id == 0) $result['menu_id'] = $object->id;
            $result['error'] = false;
            $result['response_code'] = 200;
            $result['message'] = 'Update menu completed!';
        }
        return $result;
    }
}