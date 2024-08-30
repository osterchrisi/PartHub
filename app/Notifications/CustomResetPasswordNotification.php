<?php

namespace App\Notifications;

use App\Mail\CustomResetPassword;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new CustomResetPassword($this->token, $notifiable->email))->to($notifiable->email);
    }
}
