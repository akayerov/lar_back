<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $subject;
    public $email;
    public $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $url, $email, $password)
    {
        $this->url = $url;
        $this->subject = $subject;
        $this->email = $email;
        $this->password = $password;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('emails.reset_password');
    }
}
