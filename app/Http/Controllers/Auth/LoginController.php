<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/account';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function account()
    {
        return view('account', ['user' => auth()->user()]);
    }
}
