<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
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


    /**
     * Build the message.
     *
     * @return $this
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'components.email.layout',
    //         with: [
    //             'header' => 'header',
    //             'slot' => 'slot',

    //         ]
    //     );
    // }
    
    public function build()
    {
        return $this->subject(__('Verify Email Address'))
                    ->view('components.email.layout')
                    ->with([
                        'url' => $this->verificationUrl,
                        'greeting' => __('Hellllo!'),
                        'introLines' => [__('Please click the button below to verify your email address.')],
                        'actionText' => __('Verify Email Address'),
                        'actionUrl' => $this->verificationUrl,
                        'outroLines' => [__('If you did not create an account, no further action is required.')],
                        'salutation' => __('Regards,') . '<br>' . config('app.name'),
                    ]);
    }
     // public function content()
    // {
    //     return $this->subject(__('Verify Email Address'))
    //         ->view('components.email.layout')
    //         ->with([
    //             'url' => $this->verificationUrl,
    //             'greeting' => __('Hellllo!'),
    //             'introLines' => [__('Please click the button below to verify your email address.')],
    //             'actionText' => __('Verify Email Address'),
    //             'actionUrl' => $this->verificationUrl,
    //             'outroLines' => [__('If you did not create an account, no further action is required.')],
    //             'salutation' => __('Regards,') . '<br>' . config('app.name'),
    //         ]);
    // }
}