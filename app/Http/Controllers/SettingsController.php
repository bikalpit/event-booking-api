<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\EtSettings;
class SettingsController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getOptionValue(Request $request)
    {
        $this->validate($request, [
            'event_id'=>'required',
            'boxoffice_id'=>'required',
            'option_key'=>'required',
        ]);
        
        if(!empty($request->boxoffice_id) && strtolower($request->event_id) == "null"){
            $result = EtSettings::where(['option_key' =>$request->option_key,'boxoffice_id'=>$request->boxoffice_id])->whereNull('event_id')->first();
        }else if(!empty($request->event_id) && strtolower($request->boxoffice_id) == "null"){
            $result = EtSettings::where(['option_key' =>$request->option_key,'event_id'=>$request->event_id])->whereNull('boxoffice_id')->first();
        }
                
        if(!empty($result)){
            return $this->sendResponse($result->option_value);      
        }else if(empty($result)){
            return $this->sendResponse("Not any match found.",200,false);   
        }else{
            return $this->sendResponse("Sorry! somthing wrong.",200,false);     
        }
    }
	public function getAllOptionsValue(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'nullable',
            'event_id'=>'nullable'
        ]); 

        if($request->boxoffice_id == "" && $request->event_id == "")
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }
        else if($request->boxoffice_id !== "" && $request->event_id !== "")
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }
        else if($request->boxoffice_id == "" && $request->event_id !== "")
        {
            $result = EtSettings::where('event_id',$request->event_id)->get();
        }
        else
        {
            $result = EtSettings::where('boxoffice_id',$request->boxoffice_id)->get();
        }

       if(sizeof($result)>0)
       {
            return $this->sendResponse($result);
       }
       else
       {
            return $this->sendResponse("Settings not found.",200,false);
       }
        
    }
	
	 public function setOptionValue(Request $request){
		 
		$this->validate($request, [
            'event_id'=>'nullable',
            'boxoffice_id'=>'nullable',
            'json_type'=>'required',
            'option_key'=>'required',
            'option_value'=>'required',
        ]);  
        
        if(strtolower($request->json_type) == "y"){

            if(!empty($request->boxoffice_id) && strtolower($request->event_id) == ""){
               $setting = EtSettings::updateOrCreate(
                    ['boxoffice_id' => $request->boxoffice_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => json_encode($request->option_value),
                ]);
               
            }else if(!empty($request->event_id) && strtolower($request->boxoffice_id) == ""){
                $setting = EtSettings::updateOrCreate(
                    ['event_id' => $request->event_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => json_encode($request->option_value),
                ]);
                
            }

        }else if(strtolower($request->json_type) == "n"){
        
            if(!empty($request->boxoffice_id) && strtolower($request->event_id) == ""){
                $setting = EtSettings::updateOrCreate(
                    ['boxoffice_id' => $request->boxoffice_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => $request->option_value,
                ]);
            }else if(!empty($request->event_id) && strtolower($request->boxoffice_id) == ""){
                $setting = EtSettings::updateOrCreate(
                    ['event_id' => $request->event_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => $request->option_value,
                ]);
            }
        }
        if(!empty($setting)){
            return $this->sendResponse("Option Set Sucessfully.");
        }else{
             return $this->sendResponse("Sorry! somthing wrong.",200,false);
        }
        
    }


   
}
