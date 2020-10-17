<?php
namespace App\Http\Controllers;
use App\EtTickets;
use App\EtOrders;
use App\EtEventTicket;
use App\EtCustomers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class DoorlistController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getAlldoorList(Request $request)
    {
        $this->validate($request, [
            'event_id'=>'required',
            'group_by' => 'nullable|in:ATT,BUY',
            'sort_by'  => 'required|in:firstname,lastname',
            'buyer_questions'=> 'required'
        ]);

            
           $result = EtOrders::with('customer')->where('event_id',$request->event_id)
           ->orderBy('id','desc')
           ->get(); 
           if(sizeof($result)>0)
           {
                return $this->sendResponse($result);
           } 
           else
           {
                return $this->sendResponse("No data found.",200,false);
           }
    }
}