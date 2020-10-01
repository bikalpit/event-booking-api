<?php
namespace App\Http\Controllers;
use App\EtTickets;
use App\EtOrders;
use App\EtEventTicket;
use App\EtCustomers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
class IssueTicketController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getAllIssueTicket(Request $request)
    {
        $this->validate($request, [
            'event_id'=>'required',
            'global_search' => 'nullable',//name,email,phone,orderid,ticket code
            'ticket_type' => 'required',
            'issued_fromdate' => 'nullable',
            'issued_todate' => 'nullable',
            'issued_status' => 'required|in:all,CO,P,C,VO'
        ]);

        if(empty($request->issued_todate) && empty($request->issued_fromdate) && !empty($request->global_search)){

            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'order_status' => $request->issued_status])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'order_status' => $request->issued_status])->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id])->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }
		}else if(empty($request->issued_fromdate) && !empty($request->issued_todate) && !empty($request->global_search)){
            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','<', $request->issued_todate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    where('order_date','<', $request->issued_todate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'order_status' => $request->issued_status])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','<', $request->issued_todate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'order_status' => $request->issued_status])->
                    where('order_date','<', $request->issued_todate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','<', $request->issued_todate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    where('order_date','<', $request->issued_todate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','<', $request->issued_todate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id])->
                    where('order_date','<', $request->issued_todate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }
        }else if(!empty($request->issued_fromdate) && empty($request->issued_todate) && !empty($request->global_search)){
            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','>', $request->issued_fromdate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    where('order_date','>', $request->issued_fromdate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'order_status' => $request->issued_status])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','>', $request->issued_fromdate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'order_status' => $request->issued_status])->
                    where('order_date','>', $request->issued_fromdate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','>', $request->issued_fromdate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    where('order_date','>', $request->issued_fromdate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id])->
                    whereIn('customer_id',$customer_ids)->
                    where('order_date','>', $request->issued_fromdate)->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id])->
                    where('order_date','>', $request->issued_fromdate)->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }
        }else if(!empty($request->issued_fromdate) && !empty($request->issued_todate) && !empty($request->global_search)){
			$customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                        'ticket_id' => $request->ticket_type,
                        'order_status' => $request->issued_status
                    ])->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'order_status' => $request->issued_status])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'order_status' => $request->issued_status])->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id,
                    'ticket_id' => $request->ticket_type])->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id' => $request->event_id])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
                }else{
                    $result = EtOrders::with(['customer','ticket'])->
                    where(['event_id'=>$request->event_id])->
                    whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->
                    where(['ticket_id' => $keyword])->
                    orWhere(['unique_code' => $keyword])->get();
                }
            }
		}else if(empty($request->issued_fromdate) && !empty($request->issued_todate) && empty($request->global_search)){
			
			if($request->ticket_type != 'all' && $request->issued_status != 'all'){

                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id' => $request->event_id,    
                'ticket_id'=> $request->ticket_type,
                'order_status' => $request->issued_status])->
                where('order_date','<', $request->issued_todate)->get();
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){

                $result= EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                'order_status' => $request->issued_status])->
                where('order_date','<', $request->issued_todate)->get();

            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){
                $result =EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                	'ticket_id'=> $request->ticket_type])->
                where('order_date','<', $request->issued_todate)->get();
            }else{
                $result= EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id])->
                where('order_date','<', $request->issued_todate)->get();
            }
		}else if(!empty($request->issued_fromdate) && empty($request->issued_todate) && empty($request->global_search)){
			
			if($request->ticket_type != 'all' && $request->issued_status != 'all'){

                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id' => $request->event_id,    
                'ticket_id'=> $request->ticket_type,
                'order_status' => $request->issued_status])->
                where('order_date','>', $request->issued_fromdate)->get();
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){

                $result= EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                'order_status' => $request->issued_status])->
                where('order_date','>', $request->issued_fromdate)->get();

            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){
                $result =EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                	'ticket_id'=> $request->ticket_type])->
                where('order_date','>', $request->issued_fromdate)->get();
            }else{
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id])->
                where('order_date','>', $request->issued_fromdate)->get();
            }
		}else if(!empty($request->issued_fromdate) && !empty($request->issued_todate) && empty($request->global_search)){
			if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'ticket_id' => $request->ticket_type,
                    'order_status' => $request->issued_status])->
                whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'order_status' => $request->issued_status])->
                whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'ticket_id' => $request->ticket_type])->
                whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
            }else{
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id])->
                whereBetween('order_date',array($request->issued_fromdate,$request->issued_todate))->get();
            }
		}else{
			if($request->ticket_type != 'all' && $request->issued_status != 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'ticket_id' => $request->ticket_type,
                    'order_status' => $request->issued_status])->get();
            }else if($request->ticket_type == 'all' && $request->issued_status != 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'order_status' => $request->issued_status])->get();
            }else if($request->ticket_type != 'all' && $request->issued_status == 'all'){
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id,
                    'ticket_id' => $request->ticket_type])->get();
            }else{
                $result = EtOrders::with(['customer','ticket'])->
                where(['event_id'=> $request->event_id])->get();
            }
		}
        
        
        if(!$result->isEmpty()){
            return $this->sendResponse($result);
        }else if($result->isEmpty()){
            return $this->sendResponse("Sorry! Issue Ticket Records not found.",200,false);
        }else{
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }
}