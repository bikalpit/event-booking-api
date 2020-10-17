<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $token;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $this->data['name'];
        $this->token = $this->data['token'];
    }
    public function build()
    {
        return $this->subject("Invitation","Event Jio")
                    ->view('invitation-email');
    }    
}
?>