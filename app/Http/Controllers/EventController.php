<?php

namespace App\Http\Controllers;
use App\EtEvent;
use App\EtEventSetting;
use App\EtEventTicket;
use App\EtEventImage;
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
            'sales_tax'=>'nullable',
            'ticket_ids'=>'nullable',
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

            $eventimg->unique_code = $eventobj->unique_code;
            $eventimg->event_id = $eventobj->id;
            $eventimg->image = $fileName;
            
            $save_eventimg = $eventimg->save();
        }

        if($request->default_img)
        {
            $eventimg = new EtEventImage;
            $eventimg->unique_code = $eventobj->unique_code;
            $eventimg->event_id = $eventobj->id;
            $eventimg->image = $request->default_img;
            
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
        $eventsettingobj->sales_tax = json_encode($request->sales_tax);

        $save_eventsetting = $eventsettingobj->save();

        if ($request->ticket_ids != null) {
            foreach ($request->ticket_ids as $ticket) {
                $event_ticket = new EtEventTicket;
                $event_ticket->event_id = $eventobj->unique_code;
                $event_ticket->ticket_id = $ticket;
                $save_event_ticket = $event_ticket->save();
            }
        }

        if($result)
        {
            return $this->sendResponse("Event added successfully.");      
        }
        else
        {
            return $this->sendResponse("Sorry! Something wrong.",200,false);     
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
           return $this->sendResponse("Something went wrong.",200,false); 
        }
    }

    function get_all_boxoffice_event_data(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'filter'=>'required|in:upcoming,past'
        ]);
        
        if ($request->filter == 'past') {
            $get_boxevents_info = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id])->where('end_date','<=',date('Y-m-d'))->where('end_time','<=',date('H:i:s'))->get();
        }else{
          $get_boxevents_info = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id])->where('end_date','>=',date('Y-m-d'))->where('end_time','>=',date('H:i:s'))->get();
        }

        if(count($get_boxevents_info)>0)    
        {         
            return $this->sendResponse($get_boxevents_info);      
        }     
        else      
        {       
            return $this->sendResponse("Sorry! Something Wrong",200,false);     
        }
    }

    function get_single_event_data(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);
        $single_event_data = EtEvent::where(['unique_code'=>$request->unique_code])->get();
        
        if(count($single_event_data)>0)   
        {         
            return $this->sendResponse($single_event_data);     
        }     
        else      
        {       
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);      
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
            'sales_tax'=>'nullable',
            'ticket_ids'=>'nullable',
            'image'=>'nullable',
            'default_img'=>'nullable',
        ]);

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
            $path = app()->basePath('public/event-images/');
            $fileName = $this->singleImageUpload($path, $request->image);

            $eventimg = new EtEventImage;

            $eventimg->unique_code = $request->unique_code;
            $eventimg->event_id = $Event->id;
            $eventimg->image = $fileName;
            
            $save_eventimg = $eventimg->save();
        }

        if($request->default_img)
        {
            $eventimg = new EtEventImage;
            
            $eventimg->unique_code = $eventobj->unique_code;
            $eventimg->event_id = $eventobj->id;
            $eventimg->image = $request->default_img;
            
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
            return $this->sendResponse("Sorry! Something wrong.",200,false);
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
            'start_date'=>'required',
            'end_date'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
            'event_status'=>'required|in:draft,publish'
        ]);

        $Event = EtEvent::where('unique_code',$request->unique_code)->first();

        $time = strtotime(Carbon::now());

        $eventobj = new EtEvent;
        $eventobj->unique_code = "eve".$time.rand(10,99)*rand(10,99);
        $eventobj->boxoffice_id = $Event->boxoffice_id;
        $eventobj->event_title = $request->event_title;
        $eventobj->start_date = $request->start_date;
        $eventobj->end_date = $request->end_date;
        $eventobj->start_time = $request->start_time;
        $eventobj->end_time = $request->end_time;
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

        if($result)
        {
            return $this->sendResponse("Duplicate event created successfully.");      
        }
        else
        {
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);     
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
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);     
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
}
