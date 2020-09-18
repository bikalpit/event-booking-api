<?php

namespace App\Http\Controllers;
use App\EtBoxOffice;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BoxOfficeController extends Controller
{ 

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }
    public function CreateBoxOffice(Request $request)
	{
        $this->validate($request, [
            'box_office_name'=>'required',
            'admin_id'=>'required',
            'type'=>'required',
            'country'=>'required|numeric',
            'currency'=>'required|numeric',
            'genre'=>'required',
            'genre_type'=>'required'
            ]);
            $firstCheck = EtBoxOffice::where(['admin_id'=>$request->admin_id,'box_office_name'=>$request->box_office_name])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate BoxOffice name for one business.",200,false);
			}
            $boxoffice = new EtBoxOffice;
            $time = strtotime(Carbon::now());
			$boxoffice->unique_code = "box".$time.rand(10,99)*rand(10,99);
			$boxoffice->box_office_name = $request->box_office_name;
            $boxoffice->admin_id = $request->admin_id;
            $boxoffice->type    = $request->type;
            $boxoffice->country = $request->country;
            $boxoffice->currency= $request->currency;
            $boxoffice->genre   = $request->genre;
            $boxoffice->genre_type = $request->genre_type;
            $boxoffice->email_order_notification = "N";
            $boxoffice->account_owner = "N";
            $boxoffice->hide_tailor_logo = "N";
			$result = $boxoffice->save();
			if($result)			
			{					
				return $this->sendResponse("BoxOffice Added Successfully.");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}

    }
    function get_single_boxoffice_data(Request $request){
        $this->validate($request, [
			'unique_code'=>'required'
			]);
        $get_boxoffice_info = EtBoxOffice::where(['unique_code'=>$request->unique_code])->get();
        if(count($get_boxoffice_info)>0)		
        {					
            return $this->sendResponse($get_boxoffice_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong.",200,false);			
        }
    }
    function get_all_boxoffice_data(Request $request){
        $this->validate($request, [
			'admin_id'=>'required'
			]);
        $get_boxoffice_info = EtBoxOffice::where(['admin_id'=>$request->admin_id])->get();
        if(count($get_boxoffice_info)>0)		
        {					
            return $this->sendResponse($get_boxoffice_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }
    public function BoxOfficeDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
		$result = EtBoxOffice::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("BoxOffice Deleted Sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
		}
    }
    public function BoxOfficeUpdate(Request $request)
	{
		$this->validate($request, [
            'unique_code'=>'required',
            'box_office_name'=>'required',
            'type'=>'required',
            'country'=>'required|numeric',
            'currency'=>'required|numeric',
            'genre'=>'required',
            'genre_type'=>'required'
        ]);
		$result = EtBoxOffice::where('unique_code',$request->unique_code)->update([
                'box_office_name'=>$request->box_office_name,
				'type'=>$request->box_office_name,
				'country'=>$request->country,
				'currency'=>$request->currency,
				'genre'=>$request->genre,
				'genre_type'=>$request->genre_type
				]);
		if(!empty($result))
		{
			return $this->sendResponse("BoxOffice Updated Sucessfully.");	
		}
		else
		{
			return $this->sendResponse("Something Went Wrong.",200,false);
		}
	}



   



    //

}

