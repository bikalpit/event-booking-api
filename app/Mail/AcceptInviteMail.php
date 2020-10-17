<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AcceptInviteMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $name;
    public $password;
    public function __construct($data)
    {
        $this->data = $data;
        $this->name = $this->data['name'];
        $this->password = $this->data['password'];
    }
    public function build()
    {
        return $this->subject("New Event Jio Password","Event Jio")
                    ->view('accept-invitation-email');
    }    
}
?>