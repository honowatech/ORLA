<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use App\Demo\Demo;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = AppServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Les utilisateurs démo n'entrent que par /demo : une adresse du domaine
     * réservé est refusée, même avec le bon mot de passe, avec la même
     * réponse qu'un mauvais mot de passe.
     */
    protected function attemptLogin(Request $request)
    {
        if (Demo::isReservedAddress($request->input($this->username()))) {
            return false;
        }

        return $this->guard()->attempt($this->credentials($request), $request->boolean('remember'));
    }
}
