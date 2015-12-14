<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

class Setting extends AbstractModel
{
    private static $acceptableEdit = [
        'option_key',
        'option_value'
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
    protected $table = 'settings';

    protected $primaryKey = 'id';

    public static function getAllSettings()
    {
        $result = [];
        $settings = static::get();
        if($settings)
        {
            foreach($settings as $key => $row)
            {
                $result[$row->option_key] = $row->option_value;
            }
        }
        return $result;
    }
}