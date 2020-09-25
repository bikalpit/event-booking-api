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
        
        if(!empty($request->boxoffice_id) && $request->event_id == ''){
            $result = EtSettings::where(['option_key' =>$request->option_key,'boxoffice_id'=>$request->boxoffice_id])->whereNull('event_id')->first();
        }else{
            $result = EtSettings::where(['option_key' =>$request->option_key,'event_id'=>$request->event_id])->whereNull('boxoffice_id')->first();
        }
              
        if($result) {
            return $this->sendResponse($result->option_value);      
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
        if($getalloption) {
            return $this->sendResponse($getalloption);      
        }else{
            return $this->sendResponse("Sorry! Somthing wrong.",200,false);     
        }
    }

   
}
