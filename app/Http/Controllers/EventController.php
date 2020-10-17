<?php

namespace App\Http\Controllers;
use App\EtEvent;
use App\EtEventSetting;
use App\EtEventTicket;
use App\EtEventImage;
use App\EtTickets;
use App\EtEventTicketRevenue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class EventController extends Controller
{
    public function __construct()
    {
        //
    }

    public function CreateEvent(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'event_title'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
            'venue_name'=>'nullable',
            'postal_code'=>'nullable',
            'country'=>'nullable',
            'online_event'=>'required|in:Y,N',
            'description'=>'required',
            'platform'=>'nullable|in:Z,GH,YU,HP,VM,SKY,OTH,N',
            'event_link'=>'nullable',
            'event_status'=>'required|in:draft,publish',
            'timezone'=>'required',
            'make_donation'=>'required|in:Y,N',
            'event_button_title'=>'required',
            'donation_title'=>'nullable',
            'donation_amt'=>'nullable',
            'donation_description'=>'nullable',
            'ticket_avilable'=>'required|in:PB,SDT,SIB',
            'ticket_unavilable'=>'required|in:TOS,SDT,SIB',
            'redirect_confirm_page'=>'required|in:Y,N',
            'redirect_url'=>'nullable',
            'hide_office_listing'=>'required|in:Y,N',
            'customer_access_code'=>'required|in:Y,N',
            'access_code'=>'nullable',
            'hide_share_button'=>'required|in:Y,N',
            'custom_sales_tax'=>'required|in:Y,N',
            'transaction_fee'=>'nullable',
            'currency'=>'nullable',
            'sales_tax'=>'nullable',
            'tickets'=>'nullable',
            'image'=>'nullable',
            'default_img'=>'nullable',
        ]);

        $firstCheck = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id,'event_title'=>$request->event_title])->first();
        if($firstCheck !== null)
        {
            return $this->sendResponse("System should not allow to enter duplicate Event name for Same Boxoffice.",200,false);
        }

        $time = strtotime(Carbon::now());

        $eventobj = new EtEvent;
        $eventobj->unique_code = "eve".$time.rand(10,99)*rand(10,99);
        $eventobj->boxoffice_id = $request->boxoffice_id;
        $eventobj->event_title = $request->event_title;
        $eventobj->start_date = $request->start_date;
        $eventobj->end_date = $request->end_date;
        $eventobj->start_time = $request->start_time;
        $eventobj->end_time = $request->end_time;
        $eventobj->venue_name = $request->venue_name;
        $eventobj->postal_code = $request->postal_code;
        $eventobj->country = $request->country;
        $eventobj->online_event = $request->online_event;
        $eventobj->description = $request->description;
        if ($request->platform == '') {
            $eventobj->platform = 'N';
        }else{
            $eventobj->platform = $request->platform;
        }
        $eventobj->event_link = $request->event_link;
        $eventobj->event_status = $request->event_status;
              
        $result = $eventobj->save();

        if($request->image)
        {
            $path = app()->basePath('public/event-images/');
            $fileName = $this->singleImageUpload($path, $request->image);
            
            $eventimg = new EtEventImage;

            $eventimg->unique_code = "img".$time.rand(10,99)*rand(10,99);
            $eventimg->event_id = $eventobj->unique_code;
            $eventimg->image = $fileName;
            $eventimg->type  = "upload";
            $save_eventimg = $eventimg->save();
        }

        if($request->default_img)
        {
            $eventimg = new EtEventImage;
            $eventimg->unique_code = "img".$time.rand(10,99)*rand(10,99);
            $eventimg->event_id = $eventobj->unique_code;
            $eventimg->image = $request->default_img;
            $eventimg->type  = "default";
            $save_eventimg = $eventimg->save();
        }

        $eventsettingobj = new EtEventSetting;
        $eventsettingobj->unique_code = "est".$time.rand(10,99)*rand(10,99);
        $eventsettingobj->event_id = $eventobj->unique_code;
        $eventsettingobj->timezone = $request->timezone;
        $eventsettingobj->make_donation = $request->make_donation;
        $eventsettingobj->event_button_title = $request->event_button_title;
        $eventsettingobj->donation_title = $request->donation_title;
        if ($request->donation_amt == null) {
          $eventsettingobj->donation_amt = '0.00';
        }else{
          $eventsettingobj->donation_amt = $request->donation_amt;
        }
        $eventsettingobj->donation_description = $request->donation_description;
        $eventsettingobj->ticket_avilable = $request->ticket_avilable;
        $eventsettingobj->ticket_unavilable = $request->ticket_unavilable;
        $eventsettingobj->redirect_confirm_page = $request->redirect_confirm_page;
        $eventsettingobj->redirect_url = $request->redirect_url;
        $eventsettingobj->hide_office_listing = $request->hide_office_listing;
        $eventsettingobj->customer_access_code = $request->customer_access_code;
        $eventsettingobj->access_code = $request->access_code;
        $eventsettingobj->hide_share_button = $request->hide_share_button;
        $eventsettingobj->custom_sales_tax = $request->custom_sales_tax;
        $eventsettingobj->transaction_fee = $request->transaction_fee;
        $eventsettingobj->currency = $request->currency;
        $eventsettingobj->sales_tax = json_encode($request->sales_tax);

        $save_eventsetting = $eventsettingobj->save();
        $allTicket = $request->tickets;
        if(sizeof($allTicket)>0)
        {
            foreach ($request->tickets as $ticket) {
                $time = strtotime(Carbon::now());
                try {
                    $ticketobj = new EtTickets;
                    $ticketobj->unique_code = "tck".$time.rand(10,99)*rand(10,99);
                    $ticketobj->boxoffice_id = $request->boxoffice_id;
                    $ticketobj->ticket_name = $ticket['ticket_name'];
                    if ($ticket['prize'] == null) {
                        $ticketobj->prize = '0.00';
                    }else{
                        $ticketobj->prize = $ticket['prize'];
                    }
                    $ticketobj->qty = $ticket['qty'];
                    $ticketobj->advance_setting = $ticket['advance_setting'];
                    $ticketobj->description = $ticket['description'];
                    if ($ticket['booking_fee'] == null) {
                        $ticketobj->booking_fee = '0.00';
                    }else{
                        $ticketobj->booking_fee = $ticket['booking_fee'];
                    }
                    if ($ticket['status'] == null) {
                      $ticketobj->status = 'OS';
                    }else{
                      $ticketobj->status = $ticket['status'];
                    }
                    $ticketobj->min_per_order = $ticket['min_per_order'];
                    $ticketobj->max_per_order = $ticket['max_per_order'];
                    $ticketobj->hide_untill = $ticket['hide_untill'];
                    if ($ticket['hide_untill'] == 'Y') {
                        $ticketobj->untill_date = $ticket['untill_date'];
                        $ticketobj->untill_time = $ticket['untill_time'];
                    }
                    $ticketobj->hide_after = $ticket['hide_after'];
                    if ($ticket['hide_after'] == 'Y') {
                        $ticketobj->after_date = $ticket['after_date'];
                        $ticketobj->after_time = $ticket['after_time'];
                    }
                    $ticketobj->sold_out = $ticket['sold_out'];
                    $ticketobj->show_qty = $ticket['show_qty'];
                    $ticketobj->discount = json_encode($ticket['discount']);
                    $ticketobj->untill_interval = $ticket['untill_interval'];
                    $ticketobj->after_interval = $ticket['after_interval'];
                    $add_ticket = $ticketobj->save();
    
                    $event_ticket = new EtEventTicket;
                    $event_ticket->event_id = $eventobj->unique_code;
                    $event_ticket->ticket_id = $ticketobj->unique_code;
                    $save_event_ticket = $event_ticket->save();
    
                    $ticket_revenue = new EtEventTicketRevenue;
                    $ticket_revenue->event_id = $eventobj->unique_code;
                    $ticket_revenue->ticket_id = $ticketobj->unique_code;
                    $ticket_revenue->sold = '0';
                    $ticket_revenue->remaining = $ticketobj->qty;
                    $ticket_revenue->ticket_amt = $ticketobj->prize;
                    $ticket_revenue->revenue = '0.00';
                    $event_ticket_revenue = $ticket_revenue->save();
                } catch(\Exception $e) {
                    return $this->sendResponse("Sorry! something wrong in ticket adding.",200,false);
                }
            }
        }
        

        /*if ($request->ticket_ids != null) {
            foreach ($request->ticket_ids as $ticket) {
                $event_ticket = new EtEventTicket;
                $event_ticket->event_id = $eventobj->unique_code;
                $event_ticket->ticket_id = $ticket;
                $save_event_ticket = $event_ticket->save();

                $ticket_info = EtTickets::where('unique_code',$ticket)->first();

                if ($ticket_info) {
                    $ticket_revenue = new EtEventTicketRevenue;
                    $ticket_revenue->event_id = $eventobj->unique_code;
                    $ticket_revenue->ticket_id = $ticket;
                    $ticket_revenue->sold = '0';
                    $ticket_revenue->remaining = $ticket_info->qty;
                    $ticket_revenue->ticket_amt = $ticket_info->prize;
                    $ticket_revenue->revenue = '0.00';
                    $event_ticket_revenue = $ticket_revenue->save();
                }
            }
        }*/

        if($result)
        {
            return $this->sendResponse("Event added successfully.");      
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);     
        }
    }

    public function EventDelete(Request $request)
    {  
        $this->validate($request, [
            'unique_code'=>'required'
        ]);

        $result = EtEvent::where('unique_code',$request->unique_code)->delete();    
        if($result)
        {
           return $this->sendResponse("Event Deleted Sucessfully"); 
        }
        else
        {
           return $this->sendResponse("Sorry! something wrong.",200,false); 
        }
    }

    function get_all_boxoffice_event_data(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'filter'=>'required|in:upcoming,past'
        ]);
        
        $all_event = [];
        if ($request->filter == 'past') {
            $all_event = EtEvent::with('soldout','remaining','finalRevenue','eventTickets','eventSetting','country')->where(['boxoffice_id'=>$request->boxoffice_id])->where('end_date','<=',date('Y-m-d'))->where('end_time','<=',date('H:i:s'))->get();
        }else{
            $get_boxevents_info = EtEvent::with('soldout','remaining','finalRevenue','eventTickets.tickets','eventSetting','country')->where(['boxoffice_id'=>$request->boxoffice_id])->where('end_date','>=',date('Y-m-d'))->get();
            foreach ($get_boxevents_info as $event) {
                if ($event->end_date == date('Y-m-d')) {
                    if ($event->end_time >= date('H:i:s')) {
                        $all_event[] = $event;
                    }
                }else{
                    $all_event[] = $event;
                }
            }
        }

        if(count($all_event)>0)   
        {         
            return $this->sendResponse($all_event);     
        }     
        else      
        {       
            return $this->sendResponse("Events not found.",200,false);      
        }
    }

    function get_single_event_data(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);
        $single_event_data = [];
        $allTickets = [];
        $single_event_data['event'] = EtEvent::with('eventSetting','images','country')->where(['unique_code'=>$request->unique_code])->get();
        $eventTickets = EtEventTicket::where('event_id', $request->unique_code)->get();
        foreach ($eventTickets as $ticket) {
            $ticketInfo = EtTickets::where('unique_code',$ticket->ticket_id)->first();
            $allTickets[] = $ticketInfo;
        }
        $single_event_data['tickets'] = $allTickets;
        if(count($single_event_data)>0)   
        {         
            return $this->sendResponse($single_event_data);     
        }     
        else      
        {       
            return $this->sendResponse("Boxoffice not found.",200,false);      
        }
    }

    public function EventUpdate(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required',
            'event_title'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
            'venue_name'=>'nullable',
            'postal_code'=>'nullable',
            'country'=>'nullable',
            'online_event'=>'required|in:Y,N',
            'description'=>'required',
            'platform'=>'nullable|in:Z,GH,YU,HP,VM,SKY,OTH,N',
            'event_link'=>'nullable',
            'event_status'=>'required|in:draft,publish',
            'timezone'=>'required',
            'make_donation'=>'required|in:Y,N',
            'event_button_title'=>'required',
            'donation_title'=>'nullable',
            'donation_amt'=>'nullable',
            'donation_description'=>'nullable',
            'ticket_avilable'=>'required|in:PB,SDT,SIB',
            'ticket_unavilable'=>'required|in:TOS,SDT,SIB',
            'redirect_confirm_page'=>'required|in:Y,N',
            'redirect_url'=>'nullable',
            'hide_office_listing'=>'required|in:Y,N',
            'customer_access_code'=>'required|in:Y,N',
            'access_code'=>'nullable',
            'hide_share_button'=>'required|in:Y,N',
            'custom_sales_tax'=>'required|in:Y,N',
            'transaction_fee'=>'nullable',
            'currency'=>'nullable',
            'sales_tax'=>'nullable',
            'ticket_ids'=>'nullable',
            'image'=>'nullable',
            'default_img'=>'nullable',
        ]);
        $time = strtotime(Carbon::now());
        /*$firstCheck = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id,'event_title'=>$request->event_title])->first();

        if($firstCheck !== null)
        {
            return $this->sendResponse("System should not allow to enter duplicate Event name for Same Boxoffice.",200,false);
        }*/

        if ($request->platform == '') {
            $platform = 'N';
        }else{
            $platform = $request->platform;
        }

        $result = EtEvent::where('unique_code',$request->unique_code)->update([
            'event_title'=>$request->event_title,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'venue_name'=>$request->venue_name,
            'postal_code'=>$request->postal_code,
            'country'=>$request->country,
            'online_event'=>$request->online_event,
            'description'=>$request->description,
            'platform'=>$platform,
            'event_link'=>$request->event_link,
            'event_status'=>$request->event_status
        ]);

        $Event = EtEvent::where('unique_code', $request->unique_code)->first();

        if($request->image)
        {
            $EventImage = EtEventImage::where('event_id',$request->unique_code)->first();
            if ($Event) {
                $EtImage = app()->basePath('public/event-images/'.basename($EventImage->image));
                if(basename($EventImage->image) !== "default1.jpg" || basename($EventImage->image) !== "default2.jpg" || basename($EventImage->image) !== "default3.jpg" || basename($EventImage->image) !== "default4.jpg" || basename($EventImage->image) !== "default5.jpg" || basename($EventImage->image) !== "default6.jpg"){
                  if(file_exists($EtImage)) {
                    @unlink($EtImage);
                  }
                }
                EtEventImage::where('event_id',$request->unique_code)->delete();
            }

            $path = app()->basePath('public/event-images/');
            $fileName = $this->singleImageUpload($path, $request->image);

            $eventimg = new EtEventImage;
            $eventimg->unique_code = "img".$time.rand(10,99)*rand(10,99);
            $eventimg->event_id = $request->unique_code;
            $eventimg->image = $fileName;
            $eventimg->type  = "upload";
            $save_eventimg = $eventimg->save();
        }

        if($request->default_img)
        {
            EtEventImage::where('event_id',$request->unique_code)->delete();
            $eventimg = new EtEventImage;
            $eventimg->unique_code = "img".$time.rand(10,99)*rand(10,99);
            $eventimg->event_id = $request->unique_code;
            $eventimg->image = $request->default_img;
            $eventimg->type  = "default";
            $save_eventimg = $eventimg->save();
        }

        if ($request->donation_amt == null) {
            $donation_amt = '0.00';
        }else{
            $donation_amt = $request->donation_amt;
        }

        $update_setting = EtEventSetting::where('event_id',$request->unique_code)->update([
            'timezone'=>$request->timezone,
            'make_donation'=>$request->make_donation,
            'event_button_title'=>$request->event_button_title,
            'donation_title'=>$request->donation_title,
            'donation_amt'=>$donation_amt,
            'donation_description'=>$request->donation_description,
            'ticket_avilable'=>$request->ticket_avilable,
            'ticket_unavilable'=>$request->ticket_unavilable,
            'redirect_confirm_page'=>$request->redirect_confirm_page,
            'redirect_url'=>$request->redirect_url,
            'hide_office_listing'=>$request->hide_office_listing,
            'customer_access_code'=>$request->customer_access_code,
            'access_code'=>$request->access_code,
            'hide_share_button'=>$request->hide_share_button,
            'custom_sales_tax'=>$request->custom_sales_tax,
            'transaction_fee'=>$request->transaction_fee,
            'currency'=>$request->currency,
            'sales_tax'=>json_encode($request->sales_tax)
        ]);

        if ($request->ticket_ids != null) {
            foreach ($request->ticket_ids as $ticket) {
                $event_ticket = new EtEventTicket;
                $event_ticket->event_id = $request->unique_code;
                $event_ticket->ticket_id = $ticket;
                $save_event_ticket = $event_ticket->save();
            }
        }

        if(!empty($result))
        {
            return $this->sendResponse("Event updated sucessfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }
    }

    public function getDefaultImages(Request $request)
    {
        $defaultImg = [];
        $default_images = DB::table('et_default_event_images')->get();
        foreach ($default_images as $image) {
            $path = env('APP_URL');
            $defaultImg[] = ['id'=>$image->id,'name'=>$image->name,'path'=>$path.'event-images/'.$image->name];
        }

        return $this->sendResponse($defaultImg);
    }

    public function duplicateEvent(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required',
            'event_title'=>'required',
            'start_date'=>'required|date|date_format:Y-m-d',
            'end_date'=>'required|date|date_format:Y-m-d',
            'event_status'=>'required|in:draft,publish'
        ]);

        $time = strtotime(Carbon::now());
        $Event = EtEvent::where('unique_code',$request->unique_code)->first();
        $settings = EtEventSetting::where('event_id',$request->unique_code)->first();
        if(empty($Event) && empty($settings))
        {
            return $this->sendResponse("Sorry!something wrong.",200,false);
        }
            
                $eventobj = new EtEvent;
                $eventobj->unique_code = "eve".$time.rand(10,99)*rand(10,99);
                $eventobj->boxoffice_id = $Event->boxoffice_id;
                $eventobj->event_title = $request->event_title;
                $eventobj->start_date = $request->start_date;
                $eventobj->end_date = $request->end_date;
                $eventobj->start_time = $Event->start_time;
                $eventobj->end_time = $Event->end_time;
                $eventobj->venue_name = $Event->venue_name;
                $eventobj->postal_code = $Event->postal_code;
                $eventobj->country = $Event->country;
                $eventobj->online_event = $Event->online_event;
                $eventobj->description = $Event->description;
                if ($Event->platform == '') {
                    $eventobj->platform = 'N';
                }else{
                    $eventobj->platform = $Event->platform;
                }
                $eventobj->event_link = $Event->event_link;
                $eventobj->event_status = $request->event_status;
                $result = $eventobj->save();

                $eventsettingobj = new EtEventSetting;
                $eventsettingobj->unique_code = "est".$time.rand(10,99)*rand(10,99);
                $eventsettingobj->event_id = $eventobj->unique_code;
                $eventsettingobj->timezone = $settings->timezone;
                $eventsettingobj->make_donation = $settings->make_donation;
                $eventsettingobj->event_button_title = $settings->event_button_title;
                $eventsettingobj->donation_title = $settings->donation_title;
                if ($settings->donation_amt == null) {
                  $eventsettingobj->donation_amt = '0.00';
                }else{
                  $eventsettingobj->donation_amt = $settings->donation_amt;
                }
                $eventsettingobj->donation_description = $settings->donation_description;
                $eventsettingobj->ticket_avilable = $settings->ticket_avilable;
                $eventsettingobj->ticket_unavilable = $settings->ticket_unavilable;
                $eventsettingobj->redirect_confirm_page = $settings->redirect_confirm_page;
                $eventsettingobj->redirect_url = $settings->redirect_url;
                $eventsettingobj->hide_office_listing = $settings->hide_office_listing;
                $eventsettingobj->customer_access_code = $settings->customer_access_code;
                $eventsettingobj->access_code = $settings->access_code;
                $eventsettingobj->hide_share_button = $settings->hide_share_button;
                $eventsettingobj->transaction_fee = $settings->transaction_fee;
                $eventsettingobj->currency = $settings->currency;
                $eventsettingobj->custom_sales_tax = $settings->custom_sales_tax;
                $eventsettingobj->sales_tax = json_encode($settings->sales_tax);
                $save_eventsetting = $eventsettingobj->save();
            
        if($result)
        {
            return $this->sendResponse("Duplicate event created successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }
    }

    public function updateEventStatus(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required',
            'event_status'=>'required|in:draft,publish'
        ]);

        $result = EtEvent::where('unique_code',$request->unique_code)->update(['event_status'=>$request->event_status]);

        if($result)
        {
            return $this->sendResponse("Status updated successfully.");      
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);     
        }
    }
    public function eventsList(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required'
        ]);

        $result = EtEvent::select('unique_code','event_title')->where('boxoffice_id',$request->boxoffice_id)->get();
        if(sizeof($result)>0)
        {
            return $this->sendResponse($result);
        }
        else
        {
            return $this->sendResponse("Sorry!something wrong.",200,false);
        }

    }
    public function getTimeslots(Request $request)
    {
        $this->validate($request, [
            'interval'=>'required',
        ]);

        $minDiff = $request->interval*60;

        $open_time = strtotime("00:00");
        $close_time = strtotime("23:59");

        $output = [];
        for( $i=$open_time; $i<$close_time; $i+=$minDiff) {
            $output[] = date("H:i", $i);
        };

        return $this->sendResponse($output); 
    }

    /*public function duplicateEvent(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required',
            'eventArry'=>'required'
        ]);

        $time = strtotime(Carbon::now());
        $Event = EtEvent::where('unique_code',$request->unique_code)->first();
        $eventArry = $request->eventArry;
        if(sizeof($eventArry)>0)
        {
            foreach($eventArry as $newEvent)
            {
                $eventobj = new EtEvent;
                $eventobj->unique_code = "eve".$time.rand(10,99)*rand(10,99);
                $eventobj->boxoffice_id = $Event->boxoffice_id;
                $eventobj->event_title = $newEvent['event_title'];
                $eventobj->start_date = $newEvent['start_date'];
                $eventobj->end_date = $newEvent['end_date'];
                $eventobj->start_time = $Event->start_time;
                $eventobj->end_time = $Event->end_time;
                $eventobj->venue_name = $Event->venue_name;
                $eventobj->postal_code = $Event->postal_code;
                $eventobj->country = $Event->country;
                $eventobj->online_event = $Event->online_event;
                $eventobj->description = $Event->description;
                if ($Event->platform == '') {
                    $eventobj->platform = 'N';
                }else{
                    $eventobj->platform = $Event->platform;
                }
                $eventobj->event_link = $Event->event_link;
                $eventobj->event_status = $newEvent['event_status'];
                $result = $eventobj->save();

                $eventsettingobj = new EtEventSetting;
                $eventsettingobj->unique_code = "est".$time.rand(10,99)*rand(10,99);
                $eventsettingobj->event_id = $eventobj->unique_code;
                $eventsettingobj->timezone = $newEvent['event_settings'][0]['timezone'];
                $eventsettingobj->make_donation = $newEvent['event_settings'][0]['make_donation'];
                $eventsettingobj->event_button_title = $newEvent['event_settings'][0]['event_button_title'];
                $eventsettingobj->donation_title = $newEvent['event_settings'][0]['donation_title'];
                if ($newEvent['event_settings'][0]['donation_amt'] == null) {
                  $eventsettingobj->donation_amt = '0.00';
                }else{
                  $eventsettingobj->donation_amt = $newEvent['event_settings'][0]['donation_amt'];
                }
                $eventsettingobj->donation_description = $newEvent['event_settings'][0]['donation_description'];
                $eventsettingobj->ticket_avilable = $newEvent['event_settings'][0]['ticket_avilable'];
                $eventsettingobj->ticket_unavilable = $newEvent['event_settings'][0]['ticket_unavilable'];
                $eventsettingobj->redirect_confirm_page = $newEvent['event_settings'][0]['redirect_confirm_page'];
                $eventsettingobj->redirect_url = $newEvent['event_settings'][0]['redirect_url'];
                $eventsettingobj->hide_office_listing = $newEvent['event_settings'][0]['hide_office_listing'];
                $eventsettingobj->customer_access_code = $newEvent['event_settings'][0]['customer_access_code'];
                $eventsettingobj->access_code = $newEvent['event_settings'][0]['access_code'];
                $eventsettingobj->hide_share_button = $newEvent['event_settings'][0]['hide_share_button'];
                $eventsettingobj->transaction_fee = $newEvent['event_settings'][0]['transaction_fee'];
                $eventsettingobj->currency = $newEvent['event_settings'][0]['currency'];
                $eventsettingobj->custom_sales_tax = $newEvent['event_settings'][0]['custom_sales_tax'];
                $eventsettingobj->sales_tax = json_encode($newEvent['event_settings'][0]['sales_tax']);
                $save_eventsetting = $eventsettingobj->save();
            }
        }

        if($result)
        {
            return $this->sendResponse("Duplicate event created successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }
    }*/

    public function eventTickets(Request $request)
    {
        $this->validate($request, [
            'event_id'=>'required'
        ]);

        $all_tickets = [];
        $event_tickets = EtEventTicket::where(['event_id'=>$request->event_id])->get();
        foreach ($event_tickets as $ticket) {
            $event_ticket = EtTickets::where('unique_code', $ticket->ticket_id)->first();
            $all_tickets[] = $event_ticket;
        }

        if($all_tickets)
        {
            return $this->sendResponse($all_tickets);
        }
        else
        {
            return $this->sendResponse("Sorry! Tickets not found.",200,false);
        }
    }
}
