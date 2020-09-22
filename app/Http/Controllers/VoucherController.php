<?php

namespace App\Http\Controllers;
use App\EtVoucher;
use Illuminate\Http\Request;
use Carbon\Carbon;
class VoucherController extends Controller
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
    





    public function Createvoucher(Request $request)
	{
		$this->validate($request, [
			'boxoffice_id'=>'required',
			'voucher_name'=>'required',
			'voucher_value'=>'required',
			'voucher_code'=>'required',
			'expiry_date'=>'required|date|date_format:Y-m-d',
			'event_id'=>'required'
			]);
			
        
            $firstCheck = EtVoucher::where(['boxoffice_id'=>$request->boxoffice_id,'voucher_name'=>$request->voucher_name,'voucher_code'=>$request->voucher_code])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate voucher name for single Boxoffice Id.",200,false);
			}
            $etvoucher = new EtVoucher;
            $time = strtotime(Carbon::now());
			$etvoucher->unique_code = "vou".$time.rand(10,99)*rand(10,99);
			      $etvoucher->boxoffice_id = $request->boxoffice_id;
			      $etvoucher->voucher_name = $request->voucher_name;
			      $etvoucher->voucher_value = $request->voucher_value;
			      $etvoucher->voucher_code = $request->voucher_code;
			      $etvoucher->expiry_date = $request->expiry_date;
			      $etvoucher->event_id = $request->event_id;
	
			$result = $etvoucher->save();
			if($result)			
			{					
				return $this->sendResponse("Voucher added successfully.");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
			
			
    }

    function get_voucher_data(Request $request){
        $this->validate($request, [
			'unique_code'=>'required'
			]);
        $get_voucher_info = EtVoucher::where(['unique_code'=>$request->unique_code])->get();
        
        if(count($get_voucher_info)>0)		
        {					
            return $this->sendResponse($get_voucher_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }

    function get_all_voucher_data(Request $request){
        $this->validate($request, [
			'boxoffice_id'=>'required',
			'search'=>'nullable'
			]);

			if($request->search !=''){
				$search_item = $request->search;
				$get_all_voucher_info = EtVoucher::where('boxoffice_id',$request->boxoffice_id)->where(function($query) use ($search_item) {
					$query->where('voucher_name', 'LIKE', '%'.$search_item.'%')
					->orWhere('voucher_code', 'LIKE', '%'.$search_item.'%');
					})->get();
			}else{
				$get_all_voucher_info = EtVoucher::where(['boxoffice_id'=>$request->boxoffice_id])->get();
			}
        if(count($get_all_voucher_info)>0)		
        {					
            return $this->sendResponse($get_all_voucher_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }
   
    public function VoucherDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        	
		$result = EtVoucher::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("Voucher deleted sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
		}
	}

    public function VoucherUpdate(Request $request)
	{
		$this->validate($request, [			
			'unique_code'=>'required',
			'boxoffice_id'=>'required',
			'voucher_name'=>'required',
			'voucher_value'=>'required',
			'voucher_code'=>'required',
			'expiry_date'=>'required|date|date_format:Y-m-d',
			'event_id'=>'required'
     
			]);

$firstCheck = EtVoucher::where(['boxoffice_id'=>$request->boxoffice_id,'voucher_name'=>$request->voucher_name,'voucher_code'=>$request->voucher_code])->first();
      if($firstCheck !== null)
{
  return $this->sendResponse("System should not allow to enter duplicate Coupon name for single Boxoffice Id.",200,false);
}	
		$result = EtVoucher::where('unique_code',$request->unique_code)->update([
				'voucher_name'=>$request->voucher_name,
				'voucher_value'=>$request->voucher_value,
				'voucher_code'=>$request->voucher_code,
				'expiry_date'=>$request->expiry_date,
				'event_id'=>$request->event_id
				]);
		if(!empty($result))
		{
			return $this->sendResponse("Voucher updated sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Something Went Wrong.",200,false);
		}
  }
    //
}
