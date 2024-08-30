<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomVerifyEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email Address',
        );
    }

    public function build()
    {
        return $this->subject(__('Verify Email Address'))
            ->view('mail.VerifyEmail')
            ->with([
                'actionUrl' => $this->verificationUrl,
                'actionText' => __('Verify Email Address'),
                'outroLines' => [__('If you did not create an account, no further action is required.')],
                // 'salutation' => '',
            ]);
    }
}
