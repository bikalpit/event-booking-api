<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe;

class StripeController extends Controller
{
    public function __construct()
    {
        //
    }

    public function stripePayment(Request $request)
    {
        $this->validate($request, [
            'name'=>'required',
            'number'=>'required|numeric',
            'exp_month'=>'required|numeric',
            'exp_year'=>'required',
            'cvc'=>'required',
            'amount'=>'required|numeric|gt:0',
            'boxoffice_id'=>'required|numeric'
        ]);

        $secret_key = 'sk_test_v7lck8DbRLZnOpFQJGV0NfJU';

        try {
            Stripe\Stripe::setApiKey($secret_key);
            $token = \Stripe\Token::create([
                'card' => [
                'name'=>$request->name,
                'number' => $request->number,
                'exp_month' => $request->exp_month,
                'exp_year' => $request->exp_year,
                'cvc' => $request->cvc
                ],
            ]);

            $card[] = $token['card'];
            foreach($card as $new_card)
            {
                $cardid = $new_card['id'];
            }
            $final_amt = (round($request->amount,0));  
            $customer = $this->stripeCustomer($secret_key,$request->name,$token);    
            $paymentResult = $this->stripeCreate($secret_key,$final_amt,$customer['id'],$customer['default_source']);
        
            if($paymentResult['status'] == "succeeded")
            {
                return $this->sendResponse($paymentResult);    
            }
            else
            {
                return $this->sendResponse("something went wroung.",200,false);
            }
            
        } catch(\Stripe\Exception\CardException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (\Stripe\Exception\RateLimitException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        } catch (Exception $e) {
            $msg = $e->getError()->message;
            return $this->sendResponse($msg,200,false);
        }
    }

    public function stripeCreate($stripe_secret,$amt,$customer_id,$card_id)
    {
        $orderID = strtoupper(str_replace('.','',uniqid('', true))); 
        Stripe\Stripe::setApiKey($stripe_secret);
        $payment_intent = Stripe\Charge::create ([
                "customer"=>$customer_id,
                "amount" => $amt*100,
                "currency" => "usd",
                'metadata' => array( 
                    'order_id' => $orderID 
                ),
                'source'=>$card_id, 
                "description" => "Test payment from montyset.com." 
        ]);
        return $payment_intent;
    }

    public function stripeCustomer($stripe_secret,$name,$strstripeToken)
    {
        Stripe\Stripe::setApiKey($stripe_secret);
        $customer = \Stripe\Customer::create([
          'name' => $name,
          'source'=>$strstripeToken,
          'address' => [
            'line1' => '210 sai street',
            'postal_code' => '233233',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'US',
          ],
        ]);

        return $customer;
    }
}