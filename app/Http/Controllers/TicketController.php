<?php

namespace App\Http\Controllers;
use App\EtTickets;
use Illuminate\Http\Request;
use Carbon\Carbon;
class TicketController extends Controller
{
    public function __construct()
    {
        //
    }

    public function addTicket(Request $request)
    {
        $this->validate($request, [
            'event_id'=>'required',
            'ticket_name'=>'required',
            'prize'=>'required',
            'qty'=>'required',
            'advance_setting'=>'required|in:Y,N',
            'description'=>'required',
            'booking_fee'=>'required',
            'status'=>'required|in:OS,H,ACR,DSO,DU,OVA',
            'min_per_order'=>'required',
            'max_per_order'=>'required',
            'hide_untill'=>'required|in:Y,N',
            'hide_after'=>'required|in:Y,N',
            'untill_date'=>'nullable|date|date_format:Y-m-d',
            'untill_time'=>'nullable|date_format:H:i',
            'after_date'=>'nullable|date|date_format:Y-m-d',
            'after_time'=>'nullable|date_format:H:i',
            'sold_out'=>'required|in:Y,N',
            'show_qty'=>'required|in:Y,N',
            'discount'=>'required|in:Y,N',
        ]);
        
        $time = strtotime(Carbon::now());

        $ticketobj = new EtTickets;
        $ticketobj->unique_code = "tck".$time.rand(10,99)*rand(10,99);
        $ticketobj->event_id = $request->event_id;
        $ticketobj->ticket_name = $request->ticket_name;
        if ($ticketobj->prize == null) {
            $ticketobj->prize = '0.00';
        }else{
            $ticketobj->prize = $request->prize;
        }
        $ticketobj->qty = $request->qty;
        $ticketobj->advance_setting = $request->advance_setting;
        $ticketobj->description = $request->description;
        if ($ticketobj->booking_fee == null) {
            $ticketobj->booking_fee = '0.00';
        }else{
            $ticketobj->booking_fee = $request->booking_fee;
        }
        $ticketobj->status = $request->status;
        $ticketobj->min_per_order = $request->min_per_order;
        $ticketobj->max_per_order = $request->max_per_order;
        $ticketobj->hide_untill = $request->hide_untill;
        if ($request->hide_untill == 'Y') {
            $ticketobj->untill_date = $request->untill_date;
            $ticketobj->untill_time = $request->untill_time;
        }
        $ticketobj->hide_after = $request->hide_after;
        if ($request->hide_after == 'Y') {
            $ticketobj->after_date = $request->after_date;
            $ticketobj->after_time = $request->after_time;
        }
        $ticketobj->sold_out = $request->sold_out;
        $ticketobj->show_qty = $request->show_qty;
        $ticketobj->discount = $request->discount;

        $result = $ticketobj->save();

        if($result)
        {
            return $this->sendResponse("Ticket Added Successfully");     
        }
        else
        {
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);      
        }
    }
}  