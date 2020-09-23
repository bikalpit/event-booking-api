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
            'event_id'=>'nullable',
            'ticket_name'=>'required',
            'prize'=>'required',
            'qty'=>'required',
            'advance_setting'=>'required|in:Y,N',
            'description'=>'required',
            'booking_fee'=>'required',
            'status'=>'required|in:OS,H,ACR,DSO,DU,OVA',
            'min_per_order'=>'required',
            'max_per_order'=>'required',
            'hide_untill'=>'nullable|in:Y,N',
            'hide_after'=>'nullable|in:Y,N',
            'untill_date'=>'nullable',
            'untill_time'=>'nullable',
            'after_date'=>'nullable',
            'after_time'=>'nullable',
            'sold_out'=>'required|in:Y,N',
            'show_qty'=>'required|in:Y,N',
            'discount'=>'nullable',
            'untill_interval'=>'nullable',
            'after_interval'=>'nullable',
        ]);
        
        $time = strtotime(Carbon::now());

        $ticketobj = new EtTickets;
        $ticketobj->unique_code = "tck".$time.rand(10,99)*rand(10,99);
        if ($request->event_id != '') {
            $ticketobj->event_id = $request->event_id;
        }
        $ticketobj->ticket_name = $request->ticket_name;
        if ($request->prize == null) {
            $ticketobj->prize = '0.00';
        }else{
            $ticketobj->prize = $request->prize;
        }
        $ticketobj->qty = $request->qty;
        $ticketobj->advance_setting = $request->advance_setting;
        $ticketobj->description = $request->description;
        if ($request->booking_fee == null) {
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
        $ticketobj->discount = json_encode($request->discount);
        $ticketobj->untill_interval = $request->untill_interval;
        $ticketobj->after_interval = $request->after_interval;

        $result = $ticketobj->save();
        $ticket = EtTickets::where('unique_code',$ticketobj->unique_code)->first();
        if($result)
        {
            return $this->sendResponse($ticket);
        }
        else
        {
            return $this->sendResponse("Sorry! Somthing wrong.",200,false); 
        }
    }

    public function updateTicket(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required',
            'ticket_name'=>'required',
            'prize'=>'required',
            'qty'=>'required',
            'advance_setting'=>'required|in:Y,N',
            'description'=>'required',
            'booking_fee'=>'required',
            'status'=>'required|in:OS,H,ACR,DSO,DU,OVA',
            'min_per_order'=>'required',
            'max_per_order'=>'required',
            'hide_untill'=>'nullable|in:Y,N',
            'hide_after'=>'nullable|in:Y,N',
            'untill_date'=>'nullable',
            'untill_time'=>'nullable',
            'after_date'=>'nullable',
            'after_time'=>'nullable',
            'sold_out'=>'required|in:Y,N',
            'show_qty'=>'required|in:Y,N',
            'discount'=>'nullable',
            'untill_interval'=>'nullable',
            'after_interval'=>'nullable'
        ]);
        
        if ($request->prize == null) {
            $prize = '0.00';
        }else{
            $prize = $request->prize;
        }

        if ($request->booking_fee == null) {
            $booking_fee = '0.00';
        }else{
            $booking_fee = $request->booking_fee;
        }

        if ($request->hide_after == 'Y' && $request->hide_untill == 'Y') {
            $update_data = [
                'ticket_name'=>$request->ticket_name,
                'prize'=>$prize,
                'qty'=>$request->qty,
                'advance_setting'=>$request->advance_setting,
                'description'=>$request->description,
                'booking_fee'=>$booking_fee,
                'status'=>$request->status,
                'min_per_order'=>$request->min_per_order,
                'max_per_order'=>$request->max_per_order,
                'hide_untill'=>$request->hide_untill,
                'hide_after'=>$request->hide_after,
                'untill_date'=>$request->untill_date,
                'untill_time'=>$request->untill_time,
                'after_date'=>$request->after_date,
                'after_time'=>$request->after_time,
                'sold_out'=>$request->sold_out,
                'show_qty'=>$request->show_qty,
                'discount'=>json_encode($request->discount),
                'untill_interval'=>$request->untill_interval,
                'after_interval'=>$request->after_interval
            ];
        }elseif ($request->hide_after == 'Y' && $request->hide_untill == 'N') {
            $update_data = [
                'ticket_name'=>$request->ticket_name,
                'prize'=>$prize,
                'qty'=>$request->qty,
                'advance_setting'=>$request->advance_setting,
                'description'=>$request->description,
                'booking_fee'=>$booking_fee,
                'status'=>$request->status,
                'min_per_order'=>$request->min_per_order,
                'max_per_order'=>$request->max_per_order,
                'hide_untill'=>$request->hide_untill,
                'hide_after'=>$request->hide_after,
                'after_date'=>$request->after_date,
                'after_time'=>$request->after_time,
                'sold_out'=>$request->sold_out,
                'show_qty'=>$request->show_qty,
                'discount'=>json_encode($request->discount),
                'untill_interval'=>$request->untill_interval,
                'after_interval'=>$request->after_interval
            ];
        }elseif ($request->hide_after == 'N' && $request->hide_untill == 'Y') {
            $update_data = [
                'ticket_name'=>$request->ticket_name,
                'prize'=>$prize,
                'qty'=>$request->qty,
                'advance_setting'=>$request->advance_setting,
                'description'=>$request->description,
                'booking_fee'=>$booking_fee,
                'status'=>$request->status,
                'min_per_order'=>$request->min_per_order,
                'max_per_order'=>$request->max_per_order,
                'hide_untill'=>$request->hide_untill,
                'hide_after'=>$request->hide_after,
                'untill_date'=>$request->untill_date,
                'untill_time'=>$request->untill_time,
                'sold_out'=>$request->sold_out,
                'show_qty'=>$request->show_qty,
                'discount'=>json_encode($request->discount),
                'untill_interval'=>$request->untill_interval,
                'after_interval'=>$request->after_interval
            ];
        }elseif ($request->hide_after == 'N' && $request->hide_untill == 'N') {
            $update_data = [
                'ticket_name'=>$request->ticket_name,
                'prize'=>$prize,
                'qty'=>$request->qty,
                'advance_setting'=>$request->advance_setting,
                'description'=>$request->description,
                'booking_fee'=>$booking_fee,
                'status'=>$request->status,
                'min_per_order'=>$request->min_per_order,
                'max_per_order'=>$request->max_per_order,
                'hide_untill'=>$request->hide_untill,
                'hide_after'=>$request->hide_after,
                'sold_out'=>$request->sold_out,
                'show_qty'=>$request->show_qty,
                'discount'=>json_encode($request->discount),
                'untill_interval'=>$request->untill_interval,
                'after_interval'=>$request->after_interval
            ];
        }
        
        $result = EtTickets::where('unique_code',$request->unique_code)->update($update_data);

        if($result)
        {
            return $this->sendResponse("Ticket updated successfully.");     
        }
        else
        {
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);      
        }
    }

    public function getSingleTicket(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);

        $result = EtTickets::where('unique_code',$request->unique_code)->first();

        if($result)
        {
            return $this->sendResponse($result);
        }
        else
        {
            return $this->sendResponse("Sorry! Ticket not available.",200,false);
        }
    }
}