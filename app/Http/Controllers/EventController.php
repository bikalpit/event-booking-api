<?php

namespace App\Http\Controllers;
use App\EtEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
class EventController extends Controller
{ 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    





    public function CreateEvent(Request $request)
	{
		$this->validate($request, [
        'boxoffice_id'=>'required',
        'event_title'=>'required',
        'start_date'=>'required|date|date_format:Y-m-d',
        'end_date'=>'required|date|date_format:Y-m-d',
        'start_time'=>'required|date_format:H:i',
        'end_time'=>'required|date_format:H:i',
        'venue_name'=>'required',
        'postal_code'=>'required',
        'country'=>'required',
        'online_event'=>'required|in:Y,N',
        'description'=>'required',
        'platform'=>'required|in:Z,GH,YU,HP,VM,SKY,OTH,N',
        'event_link'=>'required',
        'event_status'=>'required|in:draft,publish'
			]);
			
            $firstCheck = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id,'event_title'=>$request->event_title])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Event name for Same Boxoffice.",200,false);
			}
            $eventobj = new EtEvent;
            $time = strtotime(Carbon::now());
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
            $eventobj->platform = $request->platform;
            $eventobj->event_link = $request->event_link;
            $eventobj->event_status = $request->event_status;
            
           
	
			$result = $eventobj->save();
			if($result)			
			{					
				return $this->sendResponse("Event Added Successfully");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
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



    function get_all_boxoffice_event_data(Request $request){
        $this->validate($request, [
			'boxoffice_id'=>'required'
			]);
        $get_boxevents_info = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id])->get();
        
        if(count($get_boxevents_info)>0)		
        {					
            return $this->sendResponse($get_boxevents_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }

    function get_single_event_data(Request $request){
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
        'boxoffice_id'=>'required',
        'event_title'=>'required',
        'start_date'=>'required',
        'end_date'=>'required',
        'start_time'=>'required',
        'end_time'=>'required',
        'venue_name'=>'required',
        'postal_code'=>'required',
        'country'=>'required',
        'online_event'=>'required|in:Y,N',
        'description'=>'required',
        'platform'=>'required|in:Z,GH,YU,HP,VM,SKY,OTH,N',
        'event_link'=>'required',
        'event_status'=>'required|in:draft,publish'
                ]);

		
$firstCheck = EtEvent::where(['boxoffice_id'=>$request->boxoffice_id,'event_title'=>$request->event_title])->first();
if($firstCheck !== null)
{
    return $this->sendResponse("System should not allow to enter duplicate Event name for Same Boxoffice.",200,false);
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
				'platform'=>$request->platform,
				'event_link'=>$request->event_link,
				'event_status'=>$request->event_status
				]);
		if(!empty($result))
		{
			return $this->sendResponse("Event Updated Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something Went Wrong.",200,false);
		}
	}

    //
}
