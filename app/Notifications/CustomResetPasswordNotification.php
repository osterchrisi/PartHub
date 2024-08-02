<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Notification;
use App\Mail\CustomResetPassword;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new CustomResetPassword($this->token, $notifiable->email))->to($notifiable->email);
    }
}
