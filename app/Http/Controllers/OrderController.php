<?php

namespace App\Http\Controllers;
use App\EtOrders;
use App\EtCustomers;
use App\EtPayment;
use App\EtEventTicketRevenue;
use App\EtTickets;
use App\EtCoupon;
use App\EtWaitlist;
use App\EtEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use App\Mail\OrderResend;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
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
            'tax'=>'nullable',
            'grand_total'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'attendee_name'=>'required',
            'phone'=>'required',
            'email'=>'required',
            'customer_data'=>'nullable',
            'payment_status'=>'required|in:paid,unpaid',
            'payment_method'=>'required|in:cash,card'
        ]);

        $today = date('Y-m-d');
        $getCustomerJson = json_encode($request->customer_data);    
        if ($request->discount_code != '') {
            $couponCheck = EtCoupon::where(['coupon_code'=>$request->discount_code])->first();

            if ($couponCheck) {
                $max_limit = $couponCheck->max_redemption;
                $use = $couponCheck->used;
                $valid_date = $couponCheck->valid_till;
                if ($use >= $max_limit) {
                    return $this->sendResponse("Sorry! Coupon limit is over.",200,false);
                }elseif ($today > '2020-10-30') {
                    return $this->sendResponse("Sorry! Coupon has been expired.",200,false);
                }else{
                    $use = $couponCheck->used;
                    /*$ticket = EtTickets::where('unique_code',$request->ticket_id)->first();
                    if ($couponCheck->discount_type == 'P') {
                        $discount = $ticket->prize*$couponCheck->discount/100;
                        $coupon_amount = $ticket->prize-$discount;
                    }elseif ($couponCheck->discount_type == 'F') {
                        $coupon_amount = $ticket->prize-$couponCheck->discount;
                    }else{
                        $coupon_amount = $ticket->prize-$couponCheck->discount;
                    }*/
                }
            }else{
                return $this->sendResponse("Sorry! Invalid coupon code.",200,false);
            }
        }

        $time = strtotime(Carbon::now());

        $customersCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id, 'email'=>$request->email])->first();
        //dd($customersCheck);
        if ($customersCheck  !== null)
        {
            $customer_id = $customersCheck->unique_code;
        }else{
            $customerobj = new EtCustomers;

            $customerobj->unique_code = "cus".$time.rand(10,99)*rand(10,99);
            $customerobj->boxoffice_id = $request->boxoffice_id;
            $customerobj->email = $request->email;
            $customerobj->phone = $request->phone;
            $customerobj->firstname = $request->firstname;
            $customerobj->lastname = $request->lastname;
            $customerobj->email_verify = 'N';
            $customerobj->image = 'defaut.jpg';
            $customerobj->customer_data      = $getCustomerJson;
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
        $orderobj->tax = $request->tax;
        $orderobj->grand_total = $request->grand_total;
        $orderobj->order_status = 'P';
        $orderobj->attendee_name = $request->attendee_name;

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
            if ($revenue_info) {
                $sold = $revenue_info->sold+$request->qty;
                $remaining = $revenue_info->remaining-$request->qty;
                $prize = $ticket->prize*$request->qty;
                $revenue = $revenue_info->revenue+$prize;

                EtEventTicketRevenue::where(['event_id'=>$request->event_id,'ticket_id'=>$request->ticket_id])->update(['sold'=>$sold,'remaining'=>$remaining,'revenue'=>$revenue]);
            }

            if ($request->discount_code != '') {
                $used = $use+$request->qty;
                $coupon = EtCoupon::where(['coupon_code'=>$request->discount_code])->update(['used'=>$used]);
            }

            $customerInfo = EtCustomers::where('unique_code',$customer_id)->first();

            if ($customerInfo) {
              
                $customerobj = new EtWaitlist;

                $customerobj->unique_code = "wat".$time.rand(10,99)*rand(10,99);
                $customerobj->boxoffice_id = $request->boxoffice_id;
                $customerobj->event_id = $request->event_id;
                $customerobj->name = $customerInfo->firstname.' '.$customerInfo->lastname;
                $customerobj->email = $customerInfo->email;
                $customerobj->phone = $customerInfo->phone;
                $customerobj->status = 'BUY';

                $save_customer = $customerobj->save();
            }
        }
            
        if($result)
        {
            $customerInfo = EtCustomers::where('unique_code',$customer_id)->first();
            $ticket = EtTickets::where('unique_code',$request->ticket_id)->first();
            $event_detail = EtEvent::where('unique_code',$request->event_id)->first();
            $customer_mail_id = $customerInfo->email;
            $order_no = rand(10000000,99999999);
            $dataFirst = [
                'client_name'=>$customerInfo->firstname.' '.$customerInfo->lastname,
                'attendee_name'=>$request->attendee_name,
                'ticket_name'=>$ticket->ticket_name,
                'price'=>$ticket->prize,
                'ticket_code'=>$ticket->unique_code,
                'event_name'=>$event_detail->event_title,
                'start_date'=>$event_detail->start_date.' '.$event_detail->start_time,
                'end_date'=>$event_detail->end_date.' '.$event_detail->end_time,
                'venue'=>$event_detail->venue_name,
                'qty'=>$request->qty,
                'sub_total'=>$request->sub_total,
                'grand_total'=>$request->grand_total
            ];

            try{
                $pdf = PDF::loadView('orderPDF', $dataFirst);
            }
            catch(Exception $e) {
                return $this->sendResponse("Order PDF not created!",200,false); 
            }

            $filename = 'Order_'.$order_no.'.pdf';
            if($pdf->save('customer-invoice/'.$filename))
            {
                $this->configSMTP();
                $data = ['client_name'=>$customerInfo->firstname.' '.$customerInfo->lastname,'filename'=>$filename];
                try
                {
                    Mail::to($customer_mail_id)->send(new OrderConfirmation($data));
                }
                catch(\Exception $e){
                    // Get error here
                    return $this->sendResponse($e,200,false);
                }
            }
            else
            {
                return $this->sendResponse("Something went wrong in mail sending",200,false);
            }

            return $this->sendResponse("Order created successfully.");
        }
        else
        {
            return $this->sendResponse("Sorry! something wrong.",200,false);
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
            return $this->sendResponse("Order not found.",200,false);
        }
    }
		
	  public function ResendOrder(Request $request)
    {
        $this->validate($request, [
            'unique_code'=>'required'
        ]);

        $result_order_detail = EtOrders::where('unique_code',$request->unique_code)->first();
        $ticket_detail = EtTickets::where('unique_code',$result_order_detail->ticket_id)->first();
        $customer_detail = EtCustomers::where('unique_code',$result_order_detail->customer_id)->first();
        $event_detail = EtEvent::where('unique_code',$result_order_detail->event_id)->first();
        
				$customer_mail_id = $customer_detail->email;
				$ticket_no = rand(10000000,99999999);
        $dataFirst = [
            'client_name'=>$customer_detail->firstname.' '.$customer_detail->lastname,
            'ticket_name'=>$ticket_detail->ticket_name,
            'price'=>$ticket_detail->prize,
            'ticket_code'=>$ticket_detail->unique_code,
            'event_name'=>$event_detail->event_title,
            'event_date'=>$event_detail->start_date
        ];

        try{
            $pdf = PDF::loadView('ticketPDF', $dataFirst);
        }
        catch(Exception $e) {
            return $this->sendResponse("Ticket not created!",200,false); 
        }
        $filename = 'Ticket_'.$ticket_no.'.pdf';
        if($pdf->save('customer-invoice/'.$filename))
        {
    				$this->configSMTP();
            $data = ['client_name'=>$customer_detail->firstname.' '.$customer_detail->lastname,'ticket_name'=>$ticket_detail->ticket_name,'price'=>$ticket_detail->prize,'filename'=>$filename];
            try
            {
                Mail::to($customer_mail_id)->send(new OrderResend($data));
                return $this->sendResponse("Ticket send succesfully.");
            }
            catch(\Exception $e){
                // Get error here
                return $this->sendResponse("Sorry! Unable to send ticket, Try again.",200,false);
            }
        }
        else
        {
            return $this->sendResponse("something went wrong!",200,false);
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
            return $this->sendResponse("Sorry! something wrong.",200,false);
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

    public function exportOrders(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'report_type'=>'required| in:O,L',
            'order_details'=>'required|in:Y,N',
            'event_details'=>'required|in:Y,N',
            'buyer_details'=>'required|in:Y,N'
        ]);

        /* id,unique_code,order_date*/
        /* id, unique_code,event_title,start_date,end_date */
        /* id,name,email,phone,address,zip*/
        /* 
            $customer = ['unique_code','firstname','lastname','email','phone','address']; 
            $events   = ['unique_code','event_title','start_date','end_date'];
            $orders   = ['unique_code','event_id','order_date','customer_id'];
        */
            $orderArray = $request->orderArray;
            $eventArray = $request->eventArray;
            $buyerArray = $request->buyerArray;
             
            

        if($request->report_type == "O")
        {
            if($request->event_details == "Y" && $request->buyer_details == "Y")
            {
                $result = EtOrders::select($orderArray)->with(['customer'=>function($query)use($buyerArray){
                    $query->select($buyerArray);
                },'events'=>function($query)use($eventArray){
                    $query->select($eventArray);
                }])->where('boxoffice_id',$request->boxoffice_id)->get();
            }
            else if($request->event_details == "Y" && $request->buyer_details == "N")
            {
                $result = EtOrders::select($orderArray)->with(['events'=>function($query)use($eventArray){
                    $query->select($eventArray);
                }])->where('boxoffice_id',$request->boxoffice_id)->get();
            }
            else if($request->event_details == "N" && $request->buyer_details == "Y")
            {
                $result = EtOrders::select($orderArray)->with(['customer'=>function($query)use($buyerArray){
                    $query->select($buyerArray);
                }])->where('boxoffice_id',$request->boxoffice_id)->get();
            }
            else
            {
                $result = EtOrders::select($orderArray)->where('boxoffice_id',$request->boxoffice_id)->get();
            }
           
        }
        else
        {
            return $this->sendResponse("Sorry!something wrong.",200,false);
        }

        if(sizeof($result)>0)
        {
            return $this->sendResponse($result);
        }
        else
        {
            return $this->sendResponse("Orders not found.",200);
        }
    }

    public function orderUpdate(Request $request)
    {
        $this->validate($request, [
            'boxoffice_id'=>'required',
            'order_id'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',
            'email'=>'required',
            'address'=>'required',
            'attendee_name'=>'required',
            'customer_data'=>'required'
        ]);

        $get_json = json_encode($request->customer_data);
        $getOrder = EtOrders::select('customer_id')->where('unique_code',$request->order_id)->first();
        if($getOrder)
        {
            $firstCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id,'email'=>$request->email])
            ->whereNotIn('unique_code',[$getOrder->customer_id])
            ->first();
            if($firstCheck !== null)
            {
                return $this->sendResponse("Email id already exist.",200,false);
            }    
            else
            {
                $result = EtCustomers::where('unique_code',$getOrder->customer_id)
                ->update(['firstname'=>$request->firstname,'lastname'=>$request->lastname,
                            'phone'=>$request->phone,'address'=>$request->address,
                            'customer_data'=>$get_json]);        
                    EtOrders::where('unique_code',$request->order_id)->update(['attendee_name'=>$request->attendee_name]);
                if($result == 1)
                {
                    return $this->sendResponse("Order updated.");
                }
                else
                {
                    return $this->sendResponse("Order not updated.",200,false);
                }
            }
        }
        else
        {
            return $this->sendResponse("Sorry!something wrong.",200,false);
        }
    }
}