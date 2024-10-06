<?php

namespace App\Notifications;

use App\Mail\CustomVerifyEmailMailable;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailNotification
{

    protected $plan;
    protected $priceId;

    public function __construct($plan, $priceId)
    {
        $this->plan = $plan;
        $this->priceId = $priceId;
    }

    /**
     * Build the verification URL.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'plan' => $this->plan,
                'priceId' => $this->priceId,
            ]
        );
    }

    /**
     * Send the email verification notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new CustomVerifyEmailMailable($verificationUrl))->to($notifiable->email);
    }
}
