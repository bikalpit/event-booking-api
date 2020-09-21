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
    
    function get_coupon_data(Request $request){
        $this->validate($request, [
			'unique_code'=>'required'
			]);
        $get_coupon_info = EtCoupon::where(['unique_code'=>$request->unique_code])->get();
        
        if(count($get_coupon_info)>0)		
        {					
            return $this->sendResponse($get_coupon_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }





    public function CreateCoupon(Request $request)
	{
		$this->validate($request, [
			'boxoffice_id'=>'required',
			'coupon_title'=>'required',
			'coupon_code'=>'required',
			'valid_from'=>'required|date|date_format:Y-m-d',
			'max_redemption'=>'required',
			'discount_type'=>'required|in:P,F',
			'discount'=>'required',
			'valid_till'=>'required|date|date_format:Y-m-d'
			]);
			
        
            $firstCheck = EtCoupon::where(['boxoffice_id'=>$request->boxoffice_id,'coupon_title'=>$request->coupon_title,'coupon_code'=>$request->coupon_code])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Coupon name for single Boxoffice Id.",200,false);
			}
            $etcoupon = new EtCoupon;
            $time = strtotime(Carbon::now());
			$etcoupon->unique_code = "cou".$time.rand(10,99)*rand(10,99);
			      $etcoupon->boxoffice_id = $request->boxoffice_id;
			      $etcoupon->coupon_title = $request->coupon_title;
			      $etcoupon->coupon_code = $request->coupon_code;
			      $etcoupon->valid_from = $request->valid_from;
			      $etcoupon->max_redemption = $request->max_redemption;
			      $etcoupon->discount_type = $request->discount_type;
			      $etcoupon->discount = $request->discount;
            $etcoupon->valid_till = $request->valid_till;
            
	
			$result = $etcoupon->save();
			if($result)			
			{					
				return $this->sendResponse("Coupon Added Successfully");			
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
