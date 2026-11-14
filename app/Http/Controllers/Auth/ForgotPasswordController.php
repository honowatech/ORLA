<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Demo\Demo;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails {
        sendResetLinkEmail as private sendResetLinkEmailNormally;
    }

    /**
     * Adresse démo (domaine réservé) : ni jeton ni e-mail, et la même réponse
     * que pour une adresse inconnue.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        if (Demo::isReservedAddress($request->input('email'))) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        return $this->sendResetLinkEmailNormally($request);
    }
}
