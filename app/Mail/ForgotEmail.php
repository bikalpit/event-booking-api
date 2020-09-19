<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotEmail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $token;
    public $name;
    public function __construct($data)
    {
       $this->data = $data;
       $this->token  = $this->data['token'];
       $this->name   = $this->data['name'];
    }
    public function build()
    {
        return $this->subject("Reset Password","Event Jio")
                    ->view('forgot-email');
    }    
}
?>