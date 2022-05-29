<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Mail;
use App\Mail\Error;
use App\Mail\SaleOrderError;

class EmailNotification
{
    public static function notify($user, $message)
    {
        $sendErrorsAllowed = json_decode($user->settings, true)['sendErrors'];

        if ($sendErrorsAllowed) {
            Mail::to($user->email)->send(new Error($message));
        }
    }

    public static function orderNotify($user, $message, $data)
    {
        $sendErrorsAllowed = json_decode($user->settings, true)['sendErrors'];

        if ($sendErrorsAllowed) {
            Mail::to($user->email)->send(new SaleOrderError($message, $data));
        }
    }
}
