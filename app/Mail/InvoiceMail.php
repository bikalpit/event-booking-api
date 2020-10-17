<?php 

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceMail extends Mailable {
    use Queueable,
        SerializesModels;
    //build the message.
    public $data;
    public $filename;
    public $client_name;
    public $invoice_no;
    public function __construct($data)
    {
        $this->data = $data;
        $this->filename = $this->data['filename'];
        $this->client_name  = $this->data['client_name'];
        $this->invoice_no  = $this->data['invoice_no'];
    }
    public function build()
    {
        return $this->subject("Event Jio Invoice","Event Jio")
            ->view('invoice-pdf')
            ->attach(env('APP_URL').'customer-invoice/'.$this->filename, [
                'as' => $this->invoice_no.'.pdf',
                'mime' => 'application/pdf',
        ]);
    }    
}
?>