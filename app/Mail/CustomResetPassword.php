<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param string $token
     * @param string $email
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $resetUrl = url(config('app.url') . route('password.reset', ['token' => $this->token, 'email' => $this->email], false));

        return $this->subject(__('Reset Password Notification'))
                    ->view('mail.ResetPassword')
                    ->with([
                        'resetUrl' => $resetUrl,
                    ]);
    }
}
