<?php

namespace App\Services;

use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Mail;

class MailService
{
    public function __construct()
    {
        if (App::environment() != "production") {
            $this->receivers = ["arsandiwp@gmail.com"];
        }
    }

    function recepientChecker($email)
    {
        if (App::environment() != "production") {
            $receivers = ["arsandiwp@gmail.com"];
        } else {
            // $receivers = ["arsandiwp@gmail.com"];
            $receivers = [$email];
        }
        return $receivers;
    }

    function sendResetPasswordEmail($email, $token)
    {
        $receivers = $this->recepientChecker($email);
        FacadesMail::to($receivers)->send(new ForgotPassword($token));
    }
}
