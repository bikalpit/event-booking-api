<?php

namespace App\Http\Controllers;
use App\EtEvent;
use App\EtOrders;
use App\EtCustomers;
use App\EtTickets;
use App\EtPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use App\Mail\InvoiceMail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade as PDF;
class InvoiceController extends Controller
{
    public function __construct()
    {
        //
    }

    public function sendInvoice(Request $request)
    {
        $this->validate($request, [
            'order_id'=>'required'
        ]);

        $order = EtOrders::where('unique_code', $request->order_id)->first();
        $payment = EtPayment::where('order_id', $request->order_id)->first();
        $customerInfo = EtCustomers::where('unique_code',$order->customer_id)->first();
        $ticket = EtTickets::where('unique_code',$order->ticket_id)->first();
        $event_detail = EtEvent::where('unique_code',$order->event_id)->first();
        $invoice_no = str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT);
        $customer_mail_id = $customerInfo->email;

        $dataFirst = [
            'invoice_no'=>$invoice_no,
            'client_name'=>$customerInfo->firstname.' '.$customerInfo->lastname,
            'client_address'=>$customerInfo->address,
            'attendee_name'=>$order->attendee_name,
            'ticket_name'=>$ticket->ticket_name,
            'price'=>$ticket->prize,
            'ticket_code'=>$ticket->unique_code,
            'event_name'=>$event_detail->event_title,
            'start_date'=>$event_detail->start_date.' '.$event_detail->start_time,
            'end_date'=>$event_detail->end_date.' '.$event_detail->end_time,
            'venue'=>$event_detail->venue_name,
            'qty'=>$order->qty,
            'sub_total'=>$order->sub_total,
            'grand_total'=>$order->grand_total,
            'payment_date'=>date('M d, Y', strtotime($payment->created_at)),
            'payment_status'=>$payment->payment_status,
            'amount'=>$payment->amount
        ];

        try{
            $pdf = PDF::loadView('invoicePDF', $dataFirst);
        }
        catch(Exception $e) {
            return $this->sendResponse("Invoice PDF not created!",200,false); 
        }

        $filename = $invoice_no.'.pdf';

        if($pdf->save('customer-invoice/'.$filename))
        {
            $this->configSMTP();
            $data = ['client_name'=>$customerInfo->firstname.' '.$customerInfo->lastname,'filename'=>$filename,'invoice_no'=>$invoice_no];
            try
            {
                Mail::to('bidevs102@gmail.com')->send(new InvoiceMail($data));
            }
            catch(\Exception $e){
                // Get error here
                return $this->sendResponse('Something went wrong in mail sending',200,false);
            }

            $path = ENV('APP_URL').'customer-invoice/'.$filename;
            return $this->sendResponse($path);
        }
        else
        {
            return $this->sendResponse("Something went wrong in mail sending",200,false);
        }
    }
}