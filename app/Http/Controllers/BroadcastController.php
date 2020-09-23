<?php

namespace App\Http\Controllers;
use App\EtBroadcast;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BroadcastController extends Controller
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
    
    function get_broadcast_data(Request $request){
        $this->validate($request, [
			'unique_code'=>'required'
			]);
        $get_broadcast_info = EtBroadcast::where(['unique_code'=>$request->unique_code])->get();
        
        if(count($get_broadcast_info)>0)		
        {					
            return $this->sendResponse($get_broadcast_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }

    function get_all_broadcast_data(Request $request){
        $this->validate($request, [
			'event_id'=>'required',
			'search'=>'nullable'
			]);

			if($request->search !=''){
				$search_item = $request->search;
				$get_all_broadcast_info = EtBroadcast::where('event_id',$request->event_id)->where(function($query) use ($search_item) {
					$query->where('subject', 'LIKE', '%'.$search_item.'%')
					->orWhere('message', 'LIKE', '%'.$search_item.'%');
					})->get();
			}else{
				$get_all_broadcast_info = EtBroadcast::where(['boxoffice_id'=>$request->boxoffice_id])->get();
			}
        if(count($get_all_broadcast_info)>0)		
        {					
            return $this->sendResponse($get_all_broadcast_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }





    public function CreateBroadcast(Request $request)
	{
		$this->validate($request, [
			'event_id'=>'required',
			'recipients'=>'required',
			'subject'=>'required',
			'message'=>'required',
			'send'=>'required|in:IMM,AT_SED_DATE_TIME,AT_SED_ITR_BFO_EVT_ST,AT_SED_ITR_AFT_EVT_ND',
			'terms'=>'required'
			]);
			
        
            $firstCheck = EtBroadcast::where(['event_id'=>$request->event_id,'subject'=>$request->subject,'message'=>$request->message])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Broadcast name for single Boxoffice Id.",200,false);
			}
            $etbroadcast = new EtBroadcast;
            $time = strtotime(Carbon::now());
			$etbroadcast->unique_code = "bro".$time.rand(10,99)*rand(10,99);
			      $etbroadcast->event_id = $request->event_id;
			      $etbroadcast->recipients = $request->recipients;
			      $etbroadcast->subject = $request->subject;
			      $etbroadcast->message = $request->message;
			      $etbroadcast->send = $request->send;
			      $etbroadcast->terms = $request->terms;
	
			$result = $etbroadcast->save();
			if($result)			
			{					
				return $this->sendResponse("Broadcast added successfully.");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
			
			
    }


    public function BroadcastDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        
				
				
		$result = EtBroadcast::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("Broadcast deleted sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
		}
	}


	public function BroadcastUpdate(Request $request)
	{
		$this->validate($request, [			
			'unique_code'=>'required',
			'event_id'=>'required',
			'recipients'=>'required',
			'subject'=>'required',
			'message'=>'required',
			'send'=>'required|in:IMM,AT_SED_DATE_TIME,AT_SED_ITR_BFO_EVT_ST,AT_SED_ITR_AFT_EVT_ND',
			'terms'=>'required'
     
			]);

			$firstCheck = EtBroadcast::where(['event_id'=>$request->event_id,'subject'=>$request->subject,'message'=>$request->message])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Broadcast name for single Boxoffice Id.",200,false);
			}	
		$result = EtBroadcast::where('unique_code',$request->unique_code)->update([
				'recipients'=>$request->recipients,
				'subject'=>$request->subject,
				'message'=>$request->message,
				'send'=>$request->send,
				'terms'=>$request->terms
				]);
		if(!empty($result))
		{
			return $this->sendResponse("Broadcast updated sucessfully.");	
		}
		else
		{ 
			return $this->sendResponse("Something Went Wrong.",200,false);
		}
  }
  

 
    //
}
