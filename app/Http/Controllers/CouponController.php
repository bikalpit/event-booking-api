<?php

namespace App\Http\Controllers;
use App\EtCoupon;
use App\EtTicketCoupon;
use Illuminate\Http\Request;
use Carbon\Carbon;
class CouponController extends Controller
{
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
            return $this->sendResponse("Coupon not found.",200,false);			
        }
    }

    public function get_all_coupon_data(Request $request)
    {
        $this->validate($request, [
      			'boxoffice_id'=>'required',
      			'search'=>'nullable'
			  ]);

        $coupons = [];

  			if($request->search !='')
        {
    				$search_item = $request->search;
    				$all_coupons = EtCoupon::where('boxoffice_id',$request->boxoffice_id)->where(function($query) use ($search_item) {
    					$query->where('coupon_title', 'LIKE', '%'.$search_item.'%')
    					->orWhere('coupon_code', 'LIKE', '%'.$search_item.'%');
    					})->get();
            foreach ($all_coupons as $coupon) {
              $ticket_coupons = EtTicketCoupon::where('coupon_id',$coupon->unique_code)->pluck('ticket_id');
              $coupon['Tickets'] = $ticket_coupons;
              $coupons[] = $coupon;
            }
  			}
        else
        {
  				  $all_coupons = EtCoupon::where(['boxoffice_id'=>$request->boxoffice_id])->get();
            foreach ($all_coupons as $coupon) {
              $ticket_coupons = EtTicketCoupon::where('coupon_id',$coupon->unique_code)->pluck('ticket_id');
              $coupon['Tickets'] = $ticket_coupons;
              $coupons[] = $coupon;
            }
  			}

        if(count($coupons)>0)
        {
            return $this->sendResponse($coupons);
        }
        else
        {
            return $this->sendResponse("Coupon not found.",200,false);
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
				return $this->sendResponse("Coupon added successfully.");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! something wrong.",200,false);			
			}
			
			
    }


    public function CouponDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        
				
				
		$result = EtCoupon::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("Coupon deleted sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Sorry! something wrong.",200,false);	
		}
	}


	public function CouponUpdate(Request $request)
	{
		$this->validate($request, [			
			'unique_code'=>'required',
			'boxoffice_id'=>'required',
			'coupon_title'=>'required',
			'coupon_code'=>'required',
			'valid_from'=>'required|date|date_format:Y-m-d',
			'max_redemption'=>'required',
			'discount_type'=>'required|in:P,F',
			'discount'=>'required',
            'valid_till'=>'required|date|date_format:Y-m-d'
     
			]);

			$getCoupon = EtCoupon::where('unique_code',$request->unique_code)->first();
			if(!empty($getCoupon))
			{
				if($request->coupon_title !== $getCoupon->coupon_title)
				{
					$firstCheck = EtCoupon::where(['boxoffice_id'=>$request->boxoffice_id,'coupon_title'=>$request->coupon_title])->first();
					if($firstCheck !== null)
					{		
						return $this->sendResponse("System should not allow to enter duplicate Coupon name for single Boxoffice.",200,false);
					}
				}
			}
					$result = EtCoupon::where('unique_code',$request->unique_code)->update([
							'coupon_title'=>$request->coupon_title,
							'coupon_code'=>$request->coupon_code,
							'valid_from'=>$request->valid_from,
							'max_redemption'=>$request->max_redemption,
							'discount_type'=>$request->discount_type,
							'discount'=>$request->discount,
							'valid_till'=>$request->valid_till
							]);
		if(!empty($result))
		{
			return $this->sendResponse("Coupon updated sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Sorry! something wrong.",200,false);
		}
  }
  

  public function CouponStatusUpdate(Request $request)
	{
		$this->validate($request, [			
			'unique_code'=>'required',
			'status'=>'required|in:A,IA,E,S'
			]);

	
		$result = EtCoupon::where('unique_code',$request->unique_code)->update([
						'status'=>$request->status
				]);
		if(!empty($result))
		{
			return $this->sendResponse("Coupon status updated sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Sorry! something wrong.",200,false);
		}
	}
	

    //
}
