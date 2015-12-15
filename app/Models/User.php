<?php
namespace App\Models;

use App\Models;

use App\Models\AbstractModel;
use Illuminate\Support\Facades\Validator;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends AbstractModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
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
    protected $table = 'users';

    protected $primaryKey = 'id';

    public static function getUserByEmail($email)
    {
        $user = static::getBy(['email' => $email])->first();
        return $user;
    }
}