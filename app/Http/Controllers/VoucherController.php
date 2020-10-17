<?php

namespace App\Http\Controllers;
use App\EtVoucher;
use App\EtEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
class VoucherController extends Controller
{ 
    public function Createvoucher(Request $request)
	  {
  		  $this->validate($request, [
      			'boxoffice_id'=>'required',
      			'voucher_name'=>'required',
      			'voucher_value'=>'required',
      			'voucher_code'=>'required',
      			'expiry_date'=>'required|date|date_format:Y-m-d',
      			'event_id'=>'nullable'
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
  				  return $this->sendResponse("Sorry! Something Wrong",200,false);			
  			}
    }

    public function get_voucher_data(Request $request)
    {
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
            return $this->sendResponse("Voucher not found.",200,false);			
        }
    }

    public function get_all_voucher_data(Request $request)
    {
        $this->validate($request, [
      			'boxoffice_id'=>'required',
      			'search'=>'nullable'
  			]);

        $voucher_event = [];

  			if($request->search !=''){
    				$search_item = $request->search;
    				$vouchers = EtVoucher::where('boxoffice_id',$request->boxoffice_id)->where(function($query)use($search_item){
    					$query->where('voucher_name', 'LIKE', '%'.$search_item.'%')
    					->orWhere('voucher_code', 'LIKE', '%'.$search_item.'%');
    				})->get();
            foreach ($vouchers as $voucher) {
                $event_array = explode(',', $voucher->event_id);
                $Events = EtEvent::whereIn('unique_code', $event_array)->get();
                $voucher['Events'] = explode(',', $voucher->event_id);
                $voucher_event[] = $voucher;
            }
  			}else{
  				  $vouchers = EtVoucher::where(['boxoffice_id'=>$request->boxoffice_id])->get();
            foreach ($vouchers as $voucher) {
                $event_array = explode(',', $voucher->event_id);
                $Events = EtEvent::whereIn('unique_code', $event_array)->get();
                $voucher['Events'] = explode(',', $voucher->event_id);
                $voucher_event[] = $voucher;
            }
  			}

        if(count($voucher_event)>0)		
        {					
            return $this->sendResponse($voucher_event);			
        }			
        else			
        {				
            return $this->sendResponse("Voucher not found.",200,false);			
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
      			'event_id'=>'nullable'
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

    public function assignVoucherToEvent(Request $request)
    {
        $this->validate($request, [
            'voucher_id'=>'required',
            'event_id'=>'required'
        ]);
		

		$event_ids = implode(",",$request->event_id);
		$voucher = EtVoucher::where('unique_code',$request->voucher_id)->update(['event_id'=>$event_ids]);
		if($voucher == 1)
		{
			return $this->sendResponse("Events assign success.");
		}
		else
		{
			return $this->sendResponse("Sorry!something wrong.",200,false);
		}
    }
}
