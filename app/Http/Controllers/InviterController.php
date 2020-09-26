<?php

namespace App\Http\Controllers;
use App\EtInviters;
use Illuminate\Http\Request;
use Carbon\Carbon;
class InviterController extends Controller
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
    
    function get_inviters_data(Request $request){
        $this->validate($request, [
			'admin_id'=>'required'
			]);
        $get_inviters_info = EtInviters::where(['admin_id'=>$request->admin_id])->get();
        
        if(count($get_inviters_info)>0)		
        {					
            return $this->sendResponse($get_inviters_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }





    public function Createinviters(Request $request)
	{
		$this->validate($request, [
			'admin_id'=>'required',
			'email_id'=>'required',
			'status'=>'required|in:P,APP',
			'role'=>'required|in:SA,EO,A,OM',
			'permission'=>'required|in:A,EM,OM,OV',
			'sub_permission'=>'required'
			]);
			
        
            $firstCheck = EtInviters::where(['admin_id'=>$request->admin_id,'email_id'=>$request->email_id])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Inviter name for one admin.",200,false);
			}
            $inviters = new EtInviters;
            $time = strtotime(Carbon::now());
            $inviters->unique_code = "inv".$time.rand(10,99)*rand(10,99);
            $inviters->admin_id = $request->admin_id;
            $inviters->email_id = $request->email_id;
            $inviters->status = $request->status;
            $inviters->invite_datetime = date('Y-m-d H:i:s');
            $inviters->verify_token =  "v_token".$time.rand(10,99)*rand(10,99);
            $inviters->role = $request->role;
            $inviters->permission = $request->permission;
            $inviters->sub_permission = $request->sub_permission;
	
			$result = $inviters->save();
			if($result)			
			{					
				return $this->sendResponse("Inviter Added Successfully");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
			
			
    }


    public function InviterDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        
				
				
		$result = EtInviters::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("Inviter Deleted Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
		}
	}


	
	
	


    //
}
