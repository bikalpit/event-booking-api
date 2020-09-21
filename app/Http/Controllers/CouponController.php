<?php

namespace App\Http\Controllers;
use App\EtCoupon;
use Illuminate\Http\Request;
use Carbon\Carbon;
class SaleTaxController extends Controller
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
    
    function get_saletax_data(Request $request){
        $this->validate($request, [
			'boxoffice_id'=>'required'
			]);
        $get_saletax_info = EtCoupon::where(['boxoffice_id'=>$request->boxoffice_id])->get();
        
        if(count($get_saletax_info)>0)		
        {					
            return $this->sendResponse($get_saletax_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }





    public function Createsaletax(Request $request)
	{
		$this->validate($request, [
			'boxoffice_id'=>'required',
			'name'=>'required',
			'value'=>'required'
			]);
			
        
            $firstCheck = EtCoupon::where(['boxoffice_id'=>$request->boxoffice_id,'name'=>$request->name])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate SaleTax name for one business.",200,false);
			}
            $saletax = new EtCoupon;
            $time = strtotime(Carbon::now());
			$saletax->unique_code = "sal".$time.rand(10,99)*rand(10,99);
			$saletax->boxoffice_id = $request->boxoffice_id;
            $saletax->name = $request->name;
            $saletax->value = $request->value;
	
			$result = $saletax->save();
			if($result)			
			{					
				return $this->sendResponse("SaleTax Added Successfully");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
			
			
    }


    public function SaleTaxDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        
				
				
		$result = EtSalesTax::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("SaleTax Deleted Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
		}
	}


	public function saleTaxUpdate(Request $request)
	{
		$this->validate($request, [			
			'unique_code'=>'required',
			'boxoffice_id'=>'required',
			'name'=>'required',
			'value'=>'required'
			]);

		
		$result = EtCoupon::where('unique_code',$request->unique_code)->update([
				'unique_code'=>$request->unique_code,
				'boxoffice_id'=>$request->boxoffice_id,
				'name'=>$request->name,
				'value'=>$request->value
				]);
		if(!empty($result))
		{
			return $this->sendResponse("SaleTax Updated Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something Went Wrong.",200,false);
		}
	}
	

    //
}
