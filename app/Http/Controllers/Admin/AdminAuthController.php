<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AdminAuthController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    var $username = 'username';
    var $loginPath = 'auth';
    var $redirectTo = '/admin/dashboard';
    var $redirectPath = '/admin/dashboard';
    var $redirectToLoginPage = '/admin/auth/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest', ['except' => ['getLogout', 'postLogin', 'getLogin']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return AdminUser::create([
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function authenticated()
    {
        return redirect()->to($this->redirectTo)->with('successMessages', ['You logged in']);
    }

    public function getLogin()
    {
        $this->_unsetLoggedInAdminUser();
        return view('admin.auth.login');
    }

    public function postLogin(Request $request, AdminUser $adminUser)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if ($this->_checkAdminUser($credentials, $adminUser))
        {
            if ($throttles)
            {
                $this->clearLoginAttempts($request);
            }
            return $this->authenticated();
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->with('errorMessages', [
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    public function getLogout()
    {
        $this->_unsetLoggedInAdminUser();
        return redirect()->to($this->redirectToLoginPage)->with([
            'infoMessages' => ['You now logged out']
        ]);
    }

    public function _checkAdminUser($credentials, $adminUser)
    {
        $user = $adminUser->where('username', '=', $credentials['username'])->first();
        if(!$user) return false;

        if(!Hash::check($credentials['password'], $user->password)) return false;

        $this->_setLoggedInAdminUser($user);

        return true;
    }
}