<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\EtSettings;

class EventDetailsController extends Controller
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
    public function createOrderConfirmation(Request $request)
    {
        $this->validate($request, [			
            'box_office_id'=>'nullable',
            'event_id'=>'nullable',
			'confirmation_template'=>'required'
            ]);
            
            if($request->box_office_id == "" && $request->event_id != "")
            {
                    $result = EtSettings::updateOrCreate(['event_id'=>$request->event_id,'option_key'=>"order_confirmation"],
                    ['option_value'=>$request->confirmation_template]);
            }else if($request->box_office_id != "" && $request->event_id == "")
            {
                $result = EtSettings::updateOrCreate(
                    ['boxoffice_id' => $request->box_office_id, 'option_key' => 'order_confirmation'],
                    ['option_value' => $request->confirmation_template]
                );
            }
            else
            {
                return $this->sendResponse("Sorry! something wrong.",200,false);
            }

            if(!empty($result))
            {
                   return $this->sendResponse("Order Confirmation updated."); 
            }

            else
            {
                return $this->sendResponse("Sorry! something wrong.",200,false);
            }
    }
    public function setCheckoutForm(Request $request)
    {
        $this->validate($request, [			
            'boxoffice_id'=>'nullable',
            'event_id'=>'nullable',
			'form_fields'=>'required'
            ]);
            

            
                $setting = json_encode($request->form_fields);
                if($request->boxoffice_id == "" && $request->event_id != "")
                {
                    $result = EtSettings::updateOrCreate(['event_id'=>$request->event_id,'option_key'=>"checkout_form"],
                        ['option_value'=>$setting]);
                }
                else if($request->boxoffice_id != "" && $request->event_id == "")
                {
                    $result = EtSettings::updateOrCreate(['boxoffice_id'=>$request->boxoffice_id,'option_key'=>"checkout_form"],
                        ['option_value'=>$setting]);
                }
                else{
                    return $this->sendResponse("Sorry! something wrong.",200,false);
                }

            if($result)
            {
                return $this->sendResponse("Checkout form updated.");
            }
            else
            {
                return $this->sendResponse("Sorry! something wrong.",200,false);
            }
    }

	

    //
}
