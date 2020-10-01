<?php

namespace App\Http\Controllers;
use App\EtOrders;
use App\EtCustomers;
use App\EtPayment;
use App\EtEventTicketRevenue;
use App\EtTickets;
use Illuminate\Http\Request;
use Carbon\Carbon;
class OrderController extends Controller
{
    public function __construct()
    {
        //
    }

    public function createOrder(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'event_id'=>'required',
            'qty'=>'required',
            'ticket_id'=>'required',
            'sub_total'=>'required',
            'discount_code'=>'nullable',
            'discount_amt'=>'nullable',
            'voucher_code'=>'nullable',
            'voucher_amt'=>'nullable',
            'order_date'=>'required',
            'order_time'=>'required',
            'grand_total'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',
            'email'=>'required',
            'payment_status'=>'required|in:paid,unpaid',
            'payment_method'=>'required|in:cash,card'
        ]);

        $time = strtotime(Carbon::now());

        $firstCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id, 'email'=>$request->email])->first();
        //dd($firstCheck);
        if ($firstCheck  !== null)
        {
            $customer_id = $firstCheck->unique_code;
        }
        else
        {
            $customerobj = new EtCustomers;

            $customerobj->unique_code = "cus".$time.rand(10,99)*rand(10,99);
            $customerobj->boxoffice_id = $request->boxoffice_id;
            $customerobj->email = $request->email;
            $customerobj->phone = $request->phone;
            $customerobj->firstname = $request->firstname;
            $customerobj->lastname = $request->lastname;
            $customerobj->email_verify = 'N';
            $customerobj->image = 'defaut.jpg';

            $create_customer = $customerobj->save();

            $customer_id = $customerobj->unique_code;
        }

        $orderobj = new EtOrders;

        $orderobj->unique_code = "ord".$time.rand(10,99)*rand(10,99);
        $orderobj->boxoffice_id = $request->boxoffice_id;
        $orderobj->event_id = $request->event_id;
        $orderobj->qty = $request->qty;
        $orderobj->ticket_id = $request->ticket_id;
        $orderobj->sub_total = $request->sub_total;
        $orderobj->discount_code = $request->discount_code;
        $orderobj->discount_amt = $request->discount_amt;
        $orderobj->voucher_code = $request->voucher_code;
        $orderobj->voucher_amt = $request->voucher_amt;
        $orderobj->customer_id = $customer_id;
        $orderobj->order_date = $request->order_date;
        $orderobj->order_time = $request->order_time;
        $orderobj->grand_total = $request->grand_total;
        $orderobj->order_status = 'P';

        $result = $orderobj->save();

        if ($result) {

            $paymentobj = new EtPayment;

            $paymentobj->order_id = $orderobj->unique_code;
            $paymentobj->payment_status = $request->payment_status;
            $paymentobj->amount = $request->grand_total;
            $paymentobj->payment_method = $request->payment_method;
            //$paymentobj->transaction_id = '';
            $paymentobj->event_id = $request->event_id;
            $paymentobj->boxoffice_id = $request->boxoffice_id;
            $paymentobj->customer_id = $customer_id;

            $payment = $paymentobj->save();

            $ticket = EtTickets::where('unique_code',$request->ticket_id)->first();

            $revenue_info = EtEventTicketRevenue::where(['event_id'=>$request->event_id,'ticket_id'=>$request->ticket_id])->first();

            $sold = $revenue_info->sold+$request->qty;
            $remaining = $revenue_info->remaining-$request->qty;
            $prize = $ticket->prize*$request->qty;
            $revenue = $revenue_info->revenue+$prize;

            $revenue = EtEventTicketRevenue::where(['event_id'=>$request->event_id,'ticket_id'=>$request->ticket_id])->update(['sold'=>$sold,'remaining'=>$remaining,'revenue'=>$revenue]);
        }
            
        if($result)
        {
            return $this->sendResponse("Order created successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }

    public function getSingleOrder(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);

        $result = EtOrders::where('unique_code',$request->unique_code)->first();

        if($result)
        {
            return $this->sendResponse($result);
        }
        else
        {
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }

    public function cancelOrder(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);

        $result = EtOrders::where('unique_code',$request->unique_code)->update(['order_status'=>'C']);

        if($result)
        {
            return $this->sendResponse("Oreder canceled successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }

    public function getAllOrder(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'global_search' => 'nullable',
            'event_id' => 'required',
            'order_fromdate' => 'nullable',
            'order_todate' => 'nullable',
            'order_status' => 'required|in:all,CO,P,C,VO'
            
        ]);

        if(!empty($request->order_fromdate) && !empty($request->order_todate) && empty($request->global_search)){
           
            if($request->event_id != 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where([
                    'boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'order_status' => $request->order_status])->
                whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where([
                    'boxoffice_id'=> $request->boxoffice_id,
                    'order_status' => $request->order_status])->
                whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
            }else if($request->event_id != 'all' && $request->order_status == 'all'){
                $result = EtOrders::with(['customer'])->
                where([
                    'boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id])->
                whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
            }else{
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id])->
                whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
            }
        }else if(!empty($request->order_fromdate) && empty($request->order_todate) && empty($request->global_search)){
            if($request->event_id != 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                'event_id' => $request->event_id,    
                'order_status' => $request->order_status])->
                where('order_date','>', $request->order_fromdate)->get();
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                'order_status' => $request->order_status])->
                where('order_date','>', $request->order_fromdate)->get();
            }else if($request->event_id != 'all' && $request->order_status == 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                'event_id' => $request->event_id])->
                where('order_date','>', $request->order_fromdate)->get();
            }else{
                $result = EtOrders::with(['customer'])->
                where('boxoffice_id', $request->boxoffice_id)->
                where('order_date','>', $request->order_fromdate)->get();
            }
        }else if(!empty($request->order_todate) && empty($request->order_fromdate) && empty($request->global_search)){
            if($request->event_id != 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id,    
                    'order_status' => $request->order_status])->
                where('order_date','<', $request->order_todate)->get(); 
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'order_status' => $request->order_status])->
                where('order_date','<', $request->order_todate)->get(); 
            }else if($request->event_id != 'all' && $request->order_status == 'all'){ 
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id])->
                where('order_date','<', $request->order_todate)->get();  
            }else{
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id])->
                where('order_date','<', $request->order_todate)->get(); 
            }  

        }else if(!empty($request->order_fromdate) && empty($request->order_todate) && !empty($request->global_search)){

            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->event_id != 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'order_status' => $request->order_status])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','>', $request->order_fromdate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'order_status' => $request->order_status,
                    'ticket_id' => $keyword])->
                    where('order_date','>', $request->order_fromdate)->get(); 
                }
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                    'order_status' => $request->order_status])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','>', $request->order_fromdate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'order_status' => $request->order_status,
                    'ticket_id' => $keyword])->
                    where('order_date','>', $request->order_fromdate)->get(); 
                }
            }else if($request->event_id != 'all' && $request->order_status == 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id' => $request->event_id])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','>', $request->order_fromdate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'ticket_id' => $keyword])->
                    where('order_date','>', $request->order_fromdate)->get(); 
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where('boxoffice_id',$request->boxoffice_id)
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','>', $request->order_fromdate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'ticket_id' => $keyword])->
                    where('order_date','>', $request->order_fromdate)->get(); 
                }
            }
        }else if(empty($request->order_fromdate) && !empty($request->order_todate) && !empty($request->global_search)){

            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->event_id != 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id'=> $request->event_id,
                    'order_status'=> $request->order_status])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','<', $request->order_todate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id'=> $request->event_id,
                    'order_status'=> $request->order_status,
                    'ticket_id' => $keyword])->
                    where('order_date','<', $request->order_todate)->get(); 
                } 
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id'=> $request->boxoffice_id,
                    'order_status'=> $request->order_status])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','<', $request->order_todate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'order_status'=> $request->order_status,
                    'ticket_id' => $keyword])->
                    where('order_date','<', $request->order_todate)->get(); 
                }  
            }else if($request->event_id != 'all' && $request->order_status == 'all'){ 

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id'=> $request->event_id])
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','<', $request->order_todate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id'=> $request->event_id,
                    'ticket_id' => $keyword])->
                    where('order_date','<', $request->order_todate)->get(); 
                } 
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where('boxoffice_id',$request->boxoffice_id)
                    ->whereIn('customer_id',$customer_ids)
                    ->where('order_date','<', $request->order_todate)->get();  
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                    'ticket_id' => $keyword])->
                    where('order_date','<', $request->order_todate)->get(); 
                }
            }  

        }else if(!empty($request->order_todate) && !empty($request->order_fromdate) && !empty($request->global_search)){

            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->event_id != 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'order_status' => $request->order_status])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=>$request->boxoffice_id,
                        'event_id' =>$request->event_id,
                        'order_status' =>$request->order_status,
                        'ticket_id' => $keyword])->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id' => $request->boxoffice_id,
                    'order_status' => $request->order_status])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=>$request->boxoffice_id,
                        'order_status'=> $request->order_status,
                        'ticket_id' => $keyword])->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }
            }else if($request->event_id != 'all' && $request->order_status == 'all'){

                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id' => $request->boxoffice_id,
                    'event_id' => $request->event_id])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=>$request->boxoffice_id,
                        'event_id' => $request->event_id,
                        'ticket_id' => $keyword])->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id' => $request->boxoffice_id])->
                    whereIn('customer_id',$customer_ids)->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }else{
                    $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=>$request->boxoffice_id,
                        'ticket_id' => $keyword])->
                    whereBetween('order_date',array($request->order_fromdate,$request->order_todate))->get();
                }
            }
        }else if(empty($request->order_todate) && empty($request->order_fromdate) && !empty($request->global_search)){
            $customer_ids = '';
            $keyword = $request->global_search;
            $customer_ids = EtCustomers::where('firstname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                ->orWhere(DB::raw('concat(firstname," ",lastname)'),'LIKE' , '%'.$keyword.'%')
                ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                ->pluck('unique_code')->toArray();

            if($request->event_id != 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                        'event_id' => $request->event_id,
                        'order_status' => $request->order_status
                    ])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                   $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                        'event_id'=> $request->event_id,
                        'order_status'=> $request->order_status,
                        'ticket_id' => $keyword
                    ])->get();  
                }
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                        'order_status' => $request->order_status
                    ])->
                    whereIn('customer_id',$customer_ids)->get();
                }else{
                   $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                        'order_status'=> $request->order_status,
                        'ticket_id' => $keyword
                    ])->get();  
                }
            }else if($request->event_id != 'all' && $request->order_status == 'all'){
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where(['boxoffice_id' => $request->boxoffice_id,
                        'event_id' => $request->event_id
                    ])->
                    whereIn('customer_id',$customer_ids)->get(); 
                }else{
                   $result = EtOrders::with(['customer'])->
                    where(['boxoffice_id'=> $request->boxoffice_id,
                        'event_id' => $request->event_id,
                        'ticket_id' => $keyword
                    ])->get();  
                }
            }else{
                if(!empty($customer_ids)){
                    $result = EtOrders::with(['customer'])
                    ->where('boxoffice_id',$request->boxoffice_id)
                    ->whereIn('customer_id',$customer_ids)->get(); 
                }else{
                   $result = EtOrders::with(['customer'])
                    ->where([
                        'boxoffice_id'=> $request->boxoffice_id,
                        'ticket_id' => $keyword
                    ])->get(); 
                }
            }
        }else{
            if($request->event_id != 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id,
                    'order_status' => $request->order_status])->get();
            }else if($request->event_id == 'all' && $request->order_status != 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'order_status' => $request->order_status])->get();
            }else if($request->event_id != 'all' && $request->order_status == 'all'){
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id,
                    'event_id' => $request->event_id])->get();
            }else{
                $result = EtOrders::with(['customer'])->
                where(['boxoffice_id'=> $request->boxoffice_id])->get();
            }
            
        }
        
        if(!$result->isEmpty()){
            return $this->sendResponse($result);
        }else if($result->isEmpty()){
            return $this->sendResponse("Sorry! Order not found.",200,false);
        }else{
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }
       
}