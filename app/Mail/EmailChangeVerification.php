<?php 

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationUrl;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @param string $verificationUrl
     * @param string $userName
     */
    public function __construct($verificationUrl, $userName)
    {
        $this->verificationUrl = $verificationUrl;
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your New Email Address',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('Verify Your New Email Address'))
            ->view('mail.EmailChangeVerification')
            ->with([
                'userName' => $this->userName,
                'actionUrl' => $this->verificationUrl,
                'actionText' => __('Verify Email Address'),
                'outroLines' => [__('If you did not request this change, please ignore this email or contact support immediately.')],
            ]);
    }
}
