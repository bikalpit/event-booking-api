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
            return $this->sendResponse("Not Any Match Found.");   
        }else{
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);     
        }
    }
	public function getAllOptionsValue(Request $request)
    {
        $getalloption = [];
        $getoptions = EtSettings::all();
        foreach ($getoptions as $getoption) {
            $getalloption[] = ['id'=>$getoption->id,
            'boxoffice_id'=>$getoption->boxoffice_id,
            'event_id'=>$getoption->event_id,
            'option_key'=>$getoption->option_key,
            'option_value'=>$getoption->option_value,
            'created_at'=>$getoption->created_at,
            'updated_at'=>$getoption->updated_at
            ];
        }                
        if(!empty($getalloption)) {
            return $this->sendResponse($getalloption);      
        }else if(empty($getalloption)){
            return $this->sendResponse("No Data Found.");      
        }else{
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);     
        }
    }
	
	 public function setOptionValue(Request $request){
		 
		 var_dump($request);die;
		 
        $this->validate($request, [
            'event_id'=>'required',
            'boxoffice_id'=>'required',
            'json_type'=>'required',
            'option_key'=>'required',
            'option_value'=>'required',
        ]);  
        
        if(strtolower($request->json_type) == "y"){

            if(!empty($request->boxoffice_id) && strtolower($request->event_id) == "null"){
               $setting = EtSettings::updateOrCreate(
                    ['boxoffice_id' => $request->boxoffice_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => json_encode($request->option_value),
                ]);
               
            }else if(!empty($request->event_id) && strtolower($request->boxoffice_id) == "null"){
                $setting = EtSettings::updateOrCreate(
                    ['event_id' => $request->event_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => json_encode($request->option_value),
                ]);
                
            }

        }else if(strtolower($request->json_type) == "n"){
        
            if(!empty($request->boxoffice_id) && strtolower($request->event_id) == "null"){
                $setting = EtSettings::updateOrCreate(
                    ['boxoffice_id' => $request->boxoffice_id,
                    'option_key' => $request->option_key],
                    ['option_key' => $request->option_key,
                    'option_value'  => $request->option_value,
                ]);
            }else if(!empty($request->event_id) && strtolower($request->boxoffice_id) == "null"){
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
             return $this->sendResponse("Sorry! Somthing wrong.",200,false);
        }
        
    }


   
}
