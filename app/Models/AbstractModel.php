<?php
namespace App\Models;

use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class AbstractModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $customValidateMessages = [
        'max' => 'The max length of :attribute must be :max',
        'required' => 'The :attribute field is required',
        'unique' => 'The :attribute field is exists in database',
        'email' => 'The :attribute must be a valid email format',
        'numeric' => 'The :attribute must be a number',
        'size' => 'The :attribute must be exactly :size',
        'between' => 'The :attribute must be between :min - :max.',
        'in' => 'The :attribute must be one of the following types: :values',
        'same' => 'The :attribute and :other must match.',
        'mimes' => 'The :attribute must be one of the following types: :values',
        'integer' => 'The :attribute must be integer',
        'ip' => 'The :attribute must be a valid ip address',
        'json' => 'The :attribute must be a valid json string',
        'image' => 'The :attribute must be an image (jpeg, png, bmp, gif, or svg)',
        'string' => 'The :attribute must be a string',
        'timezone' => 'The :attribute must be a valid timezone identifier according to the timezone_identifiers_list PHP function.',
        'array' => 'The :attribute must be an array',
        'boolean' => 'The :attribute must be able to be cast as a boolean',
        'confirmed' => 'The :attribute must have matching field'
    ];

    private $errors = null;

    protected $rules = array();

    /**
     * Validate data when update or create.
     * @param $data: ['field_1' => 'value_1', 'field_2' => 'value_2'].
     * @param $rules: the rules to validate.
     * @param $customValidateMessages: custom message for error handle.
     * @param $justUpdateSomeFields: if true, just validate items that exists in $data.
     * @return bool
     **/
    public function validateData($data, $rules = null, $customValidateMessages = null, $justUpdateSomeFields = false)
    {
        if(!$rules) $rules = $this->rules;
        if(!$customValidateMessages) $customValidateMessages = $this->customValidateMessages;
        $result = Validator::make($data, $rules, $customValidateMessages);
        if($result->fails())
        {
            $this->errors = $result->messages()->toArray();
            if($justUpdateSomeFields == true)
            {
                $messages = [];
                foreach($data as $key => $row)
                {
                    if(array_key_exists($key, $this->errors))
                    {
                        $messages[$key] = $this->errors[$key];
                    }
                }
                $this->errors = $messages;
                if(sizeof($this->errors) > 0) return false;
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Get all validate errors, no key.
     * @return array
     **/
    public function getErrors()
    {
        $messages = [];
        foreach($this->errors as $key => $row)
        {
            foreach($row as $keyRow => $valueRow)
            {
                array_push($messages, $valueRow);
            }
        }
        return $messages;
    }

    /**
     * Get all validate errors, with the key of errors.
     * @return array
     **/
    public function getErrorsWithKey()
    {
        return $this->errors;
    }

    /**
     * Get all validate errors, with error state and http response code.
     * @return array
     **/
    public function getErrorsWithResponse()
    {
        $result = [
            'error' => false,
            'response_code' => 200
        ];
        $message = $this->getErrors();
        if($message)
        {
            $result['error'] = true;
            $result['response_code'] = 500;
            $result['message'] = $message;
        }
        return $result;
    }

    /**
     * Check if when user try to update db, but values are not changed
     * @return bool
     **/
    public function checkValueNotChange($object, $data, $error = null)
    {
        if($error == null) $error = $this->getErrorsWithKey();
        foreach ($data as $key => $row)
        {
            if($object->{$key} == $data[$key] && array_key_exists($key, $error))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Find item by fields. If not exists => create a new one.
     * @param $options: ['field_1' => 'value_1', 'field_2' => 'value_2'].
     * @return mixed
     **/
    public static function findByFieldOrCreate($options)
    {
        $obj = static::where($options)->first();
        return $obj ?: new static;
    }

    /**
     * Get all items. No params. Just accept order and per_page
     * @param $order: [*order_by* => *order_type*]
     * @param $perPage: how many items per page. If < 1, will return all items.
     * @return mixed
     **/
    public static function getAll($order = null, $perPage = 0)
    {
        $query = new static;
        if($order && is_array($order))
        {
            foreach ($order as $key => $value)
            {
                $query = $query->orderBy($key, $value);
            }
        }
        if($perPage < 1)
        {
            return $query->get();
        }
        return $query->paginate($perPage);
    }

    /**
     * Get items. Accept params.
     * @param $fields: ['field_1' => 'value_1', 'field_2' => 'value_2'].
     * @param $order: [*order_by* => *order_type*].
     * @param $multiple: get many items or just the first one.
     * @param $perPage: how many items per page. If < 1, will return all items.
     * @return mixed
     **/
    public static function getBy($fields, $order = null, $multiple = false, $perPage = 0)
    {
        $obj = new static;
        if($fields && is_array($fields) && sizeof($fields) > 0)
        {
            $obj = $obj->where($fields);
        }
        if($order && is_array($order))
        {
            foreach ($order as $key => $value)
            {
                $obj = $obj->orderBy($key, $value);
            }
        }

        if($multiple)
        {
            if($perPage > 0) return $obj->paginate($perPage);
            return $obj->get();
        }
        return $obj->first();
    }

    /**
     * Search items. Accept params.
     * @param $fields: ['field_1' => ['compare' => '=', 'value' => 'value'], 'field_2' => ['compare' => 'LIKE', 'value' => 'value']].
     * @param $order: [*order_by* => *order_type*].
     * @param $multiple: get many items or just the first one.
     * @param $perPage: how many items per page. If < 1, will return all items.
     * @return mixed
     **/
    public static function searchBy($fields, $order = null, $multiple = false, $perPage = 0)
    {
        $obj = new static;
        if($fields && is_array($fields))
        {
            foreach($fields as $key => $row)
            {
                $obj = $obj->where(function($q) use ($key, $row){

                    if($row['compare'] == 'LIKE')
                    {
                        $q->where($key, $row['compare'], $row['value']);
                    }
                    else
                    {
                        $q->where($key, $row['compare'], '%'.$row['value'].'%');
                    }
                });
            }
        }
        if($order && is_array($order))
        {
            foreach ($order as $key => $value)
            {
                $obj = $obj->orderBy($key, $value);
            }
        }

        if($multiple)
        {
            if($perPage > 0) return $obj->paginate($perPage);
            return $obj->get();
        }
        return $obj->first();
    }

    /**
     * Get item by id.
     * @param $id: id of item.
     * @return object
     **/
    public static function getById($id)
    {
        $obj = static::where('id', '=', $id)->first();
        return $obj;
    }
}