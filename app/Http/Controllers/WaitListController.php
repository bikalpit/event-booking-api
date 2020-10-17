<?php

namespace App\Http\Controllers;
use App\EtWaitlist;
use App\EtCustomers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class WaitListController extends Controller
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
    public function getWaitList(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'event_id'=>'required'
            ]);
            $finalArry = [];
            $status = ['ALL','NEW','BUY','NOTIFY'];
            foreach($status as $new_status)
            {
                if($new_status == "ALL")
                {
                    $allCount = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id])
                                ->whereIn('status',['NEW','NOTIFY'])->count();
                    $finalArry['ALL'] = $allCount;
                }
                else if($new_status == "BUY")
                {
                    $buyCount = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$new_status])->count();
                    $finalArry['BUY'] = $buyCount;
                }
                else if($new_status == "NEW")
                {
                    $newCount = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$new_status])->count();
                    $finalArry['NEW'] = $newCount;
                }
                else
                {
                    $notifyCount = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$new_status])->count();
                    $finalArry['NOTIFY'] = $notifyCount;
                }
                
            }
            return $this->sendResponse($finalArry);
            
    }
    public function createWaitList(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'event_id'=>'required',
            'name'=>'required',
            'email'=>'required|email',
            'phone'=>'required|numeric|min:6,max:16'
        ]);
        
        $checkCustomer = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id,'email'=>$request->email])->first();
        if ($checkCustomer) {
            $signed_up = 'Y';
        }else{
            $signed_up = 'N';
        }

        $firstCheck = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'email'=>$request->email])->first();
        if($firstCheck !== null)
        {
            return $this->sendResponse("Email already used for this event.",200,false);
        }

        $waitlist = new EtWaitlist;
        $time = strtotime(Carbon::now());
        $waitlist->unique_code   = 'wat'.$time.rand(10,99)*rand(10,99);
        $waitlist->boxoffice_id = $request->boxoffice_id;
        $waitlist->event_id  = $request->event_id;
        $waitlist->name = $request->name;
        $waitlist->email = $request->email;
        $waitlist->phone = $request->phone;
        $waitlist->status = "NEW";
        $waitlist->signed_up = $signed_up;
        if($waitlist->save())
        {
            return $this->sendResponse("Waitlist added successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
        }

    }
    public function getwaitingListUser(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'event_id'=>'required',
            'status'=> 'required|in:ALL,BUY,NEW,NOTIFY',
            'search'=> 'nullable'
        ]);

        $result = [];
        if($request->status == "ALL")
        {
            if($request->search == null)
            {
                $result['data'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id])->get();
                $result['count'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id])->count();
            }
            else
            {
                $result['data'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id])
                ->where('email','like','%'.$request->search.'%')
                ->get();
                $result['count'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id])
                ->where('email','like','%'.$request->search.'%')
                ->count();
            }
        }
        else
        {
            if($request->search == null)
            {
                $result['data'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$request->status])->get();
                $result['count'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$request->status])->count();
            }
            else
            {
                $result['data'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$request->status])
                ->where('email','like','%'.$request->search.'%')
                ->get();
                $result['count'] = EtWaitlist::where(['boxoffice_id'=>$request->boxoffice_id,'event_id'=>$request->event_id,'status'=>$request->status])
                ->where('email','like','%'.$request->search.'%')
                ->count();
            }
        }
        //dump(DB::getQueryLog());
        if(sizeof($result)>0)
        {
            return $this->sendResponse($result);
        }
        else
        {
            return $this->sendResponse("Waiting list not found.",200,false);
        }
    }

    //
}
