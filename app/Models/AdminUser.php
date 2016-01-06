<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Hash;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class AdminUser extends AbstractModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    public function __construct()
    {
        parent::__construct();
    }
    use Authenticatable, Authorizable, CanResetPassword;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_users';

    protected $primaryKey = 'id';

    public static function authenticate($username, $password)
    {
        $user = static::getBy([
            'username' => $username,
            'status' => 1
        ]);
        if(!$user) return null;
        if(!Hash::check($password, $user->password)) return null;
        return $user;
    }
}