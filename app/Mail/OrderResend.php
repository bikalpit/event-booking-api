<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderResend extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $filename;
    public $ticket_name;
    public $client_name;
    public $price;
    public function __construct($data)
    {
        $this->data = $data;
        $this->filename = $this->data['filename']; 
        $this->ticket_name  = $this->data['ticket_name'];
        $this->client_name  = $this->data['client_name'];
        $this->price   = $this->data['price'];
    }
    public function build()
    {
        return $this->subject("Event Jio Ticket","Event Jio")
            ->view('order-resend')
            ->attach(env('APP_URL').'customer-invoice/'.$this->filename, [
                'as' => 'Ticket.pdf',
                'mime' => 'application/pdf',
        ]);
    }    
}
?>