<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

class MenuNode extends AbstractModel
{
    private static $acceptableEdit = [
        'parent_id',
        'related_id',
        'url',
        'icon_font',
        'position',
        'title',
        'css_class',
        'parent_id'
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
    protected $table = 'menu_nodes';

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

    public function parent()
    {
        return $this->belongsTo('App\Models\MenuNode', 'parent_id');
    }

    public function child()
    {
        return $this->hasMany('App\Models\MenuNode', 'parent_id');
    }
}