<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmation extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $filename;
    public $client_name;
    public function __construct($data)
    {
        $this->data = $data;
        $this->filename = $this->data['filename'];
        $this->client_name  = $this->data['client_name'];
    }
    public function build()
    {
        return $this->subject("Event Jio Ticket","Event Jio")
            ->view('order-confirmation')
            ->attach(env('APP_URL').'customer-invoice/'.$this->filename, [
                'as' => 'TicketInfo.pdf',
                'mime' => 'application/pdf',
        ]);
    }    
}
?>