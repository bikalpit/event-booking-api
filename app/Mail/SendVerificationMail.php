<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $verification_token;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $data['name'];
        $this->verification_token = $data['verification_token'];
    }
    public function build()
    {
        return $this->subject("E-mail Verification")
                    ->view('send-verification-mail');
    }    
}
?>