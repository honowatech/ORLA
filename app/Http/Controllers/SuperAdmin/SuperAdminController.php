<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\SuperAdmin\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    // ouvrir la page de connexion du Super Admin
    public function login()
    {
        $check = check_superadmin();
        if ($check == 'true') {
            return redirect()->route('SuperAdmin.home');
        }

        return view('superadmin.login');
    }

    public function connect(Request $request)
    {
        $check = check_superadmin();
        if ($check == 'true') {
            return redirect()->route('SuperAdmin.home');
        }

        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where('email', $email)->first();
        $back = false;
        $message = '';
        $check_password = false;
        $dernier_url = session()->has('dernier_url') ? session()->get('dernier_url') : route('SuperAdmin.home');
        if ($user == null) {
            $message = 'Nous ne trouvons pas votre email';
            $back = true;
        } else {
            $check_password = password_verify($password, $user->password);
            if ($check_password == false) {
                $message = 'Vos informations ne correspondent pas';
                $back = true;
            }
        }
        if ($back == true) {
            // ///// L'utilisateur n'a pas pu se connecter...
            session()->flash('email', [
                'value' => $email,
                'error' => $message,
            ]);

            return redirect()->back();
        } else {
            session()->put('SuperAdmin_infos', ['connect' => true, 'infos' => $user]);
        }

        // dd($dernier_url,$check_password,$email,$password,$user, $back);
        return redirect($dernier_url);
    }

    // ////////////////: Déconnecter le Super Admin
    public function disconnect()
    {
        $check = check_superadmin();
        if ($check != 'true') {
            return redirect()->route($check);
        }
        session()->forget('dernier_url');
        session()->forget('SuperAdmin_infos');

        return redirect()->route('SuperAdmin.login');
    }

    public function home()
    {
        return view('superadmin.pages.home');
    }
}
