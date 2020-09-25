<?php

namespace App\Http\Controllers;
use App\EtOrders;
use App\EtCustomers;
use App\EtPayment;
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
}