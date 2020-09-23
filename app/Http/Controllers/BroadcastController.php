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
			'boxoffice_id'=>'required',
			'search'=>'nullable'
			]);

			if($request->search !=''){
				$search_item = $request->search;
				$get_all_broadcast_info = EtBroadcast::where('boxoffice_id',$request->boxoffice_id)->where(function($query) use ($search_item) {
					$query->where('broadcast_title', 'LIKE', '%'.$search_item.'%')
					->orWhere('broadcast_code', 'LIKE', '%'.$search_item.'%');
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
			'boxoffice_id'=>'required',
			'broadcast_title'=>'required',
			'broadcast_code'=>'required',
			'valid_from'=>'required|date|date_format:Y-m-d',
			'max_redemption'=>'required',
			'discount_type'=>'required|in:P,F',
			'discount'=>'required',
            'valid_till'=>'required|date|date_format:Y-m-d'
			]);
			
        
            $firstCheck = EtBroadcast::where(['boxoffice_id'=>$request->boxoffice_id,'broadcast_title'=>$request->broadcast_title,'broadcast_code'=>$request->broadcast_code])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Broadcast name for single Boxoffice Id.",200,false);
			}
            $etbroadcast = new EtBroadcast;
            $time = strtotime(Carbon::now());
			$etbroadcast->unique_code = "cou".$time.rand(10,99)*rand(10,99);
			      $etbroadcast->boxoffice_id = $request->boxoffice_id;
			      $etbroadcast->broadcast_title = $request->broadcast_title;
			      $etbroadcast->broadcast_code = $request->broadcast_code;
			      $etbroadcast->valid_from = $request->valid_from;
			      $etbroadcast->max_redemption = $request->max_redemption;
			      $etbroadcast->discount_type = $request->discount_type;
			      $etbroadcast->discount = $request->discount;
                  $etbroadcast->valid_till = $request->valid_till;
           
            
	
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
			'boxoffice_id'=>'required',
			'broadcast_title'=>'required',
			'broadcast_code'=>'required',
			'valid_from'=>'required|date|date_format:Y-m-d',
			'max_redemption'=>'required',
			'discount_type'=>'required|in:P,F',
			'discount'=>'required',
            'valid_till'=>'required|date|date_format:Y-m-d'
     
			]);

$firstCheck = EtBroadcast::where(['boxoffice_id'=>$request->boxoffice_id,'broadcast_title'=>$request->broadcast_title,'broadcast_code'=>$request->broadcast_code])->first();
      if($firstCheck !== null)
{
  return $this->sendResponse("System should not allow to enter duplicate Broadcast name for single Boxoffice Id.",200,false);
}	
		$result = EtBroadcast::where('unique_code',$request->unique_code)->update([
				'broadcast_title'=>$request->broadcast_title,
				'broadcast_code'=>$request->broadcast_code,
				'valid_from'=>$request->valid_from,
				'max_redemption'=>$request->max_redemption,
				'discount_type'=>$request->discount_type,
				'discount'=>$request->discount,
				'valid_till'=>$request->valid_till
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
