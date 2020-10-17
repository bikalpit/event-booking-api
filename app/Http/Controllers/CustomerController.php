<?php

namespace App\Http\Controllers;
use App\EtCustomers;
use App\EtOrders;
use App\EtEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
class CustomerController extends Controller
{
  public function __construct()
  {
      // testing
  }
  
  public function get_single_customer_data(Request $request)
  {
      $this->validate($request, [
		      'unique_code'=>'required'
      ]);

      $upcoming = [];
      $comelete = [];
      $today = Carbon::Today();
      $get_single_customer_info = EtCustomers::where(['unique_code'=>$request->unique_code])->first();
      $getAllEventid = EtOrders::where('customer_id',$request->unique_code)->pluck('event_id')->toArray();
      //dump($getAllEventid);
      $getUniqueEvents = array_unique($getAllEventid);
      //dump($getUniqueEvents);
      //dd("hello");
      $getAllEventsDetails = EtEvent::whereIn('unique_code',$getUniqueEvents)->get();
      foreach($getAllEventsDetails as $new_event)
      {
          $start_date = Carbon::parse($new_event['start_date']);
          if($start_date->gt($today))
          {
              $upcoming[] = $new_event; 
          }
          else
          {
              $comelete[] = $new_event;
          }
      }

      $finalArry = ['customer'=>$get_single_customer_info,'all'=>$getAllEventsDetails,'upcoming'=>$upcoming,'complete'=>$comelete];
      if($get_single_customer_info)		
      {					
          return $this->sendResponse($finalArry);			
      }			
      else			
      {				
          return $this->sendResponse("Customer not found.",200,false);			
      }
  }

  public function get_all_customer_data(Request $request)
  {
      $this->validate($request, [
		        'boxoffice_id'=>'required',
                'search'=>'nullable',
                'event_id'=>'nullable'
		  ]);
          
      if($request->event_id =='')
      {
          if($request->search !=''){
              $search_item = $request->search;
              $get_all_customer_info = EtCustomers::where('boxoffice_id',$request->boxoffice_id)->where(function($query) use ($search_item) {
                  $query->where('email', 'LIKE', '%'.$search_item.'%')
                  ->orWhere('firstname', 'LIKE', '%'.$search_item.'%')->orWhere('lastname', 'LIKE', '%'.$search_item.'%')->orWhere('phone', 'LIKE', '%'.$search_item.'%');
                  })->get();
          }else{
              $get_all_customer_info = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id])->get();
          }
      }
      else
      {
          $allCustomer = EtOrders::where('event_id',$request->event_id)->pluck('customer_id')->toArray();
          if(sizeof($allCustomer)>0)
          {
              if($request->search !=''){
                  $search_item = $request->search;
                  $get_all_customer_info = EtCustomers::where('boxoffice_id',$request->boxoffice_id)->where(function($query) use ($search_item) {
                      $query->where('email', 'LIKE', '%'.$search_item.'%')
                      ->orWhere('firstname', 'LIKE', '%'.$search_item.'%')->orWhere('lastname', 'LIKE', '%'.$search_item.'%')->orWhere('phone', 'LIKE', '%'.$search_item.'%');
                      })
                      ->whereIn('unique_code',$allCustomer)
                      ->get();
              }else{
                  $get_all_customer_info = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id])
                  ->whereIn('unique_code',$allCustomer)
                  ->get();
              }
          }
          else
          {
              $get_all_customer_info =  [];
          }
          
      }

      if(count($get_all_customer_info)>0)		
      {					
          return $this->sendResponse($get_all_customer_info);			
      }			
      else			
      {				
          return $this->sendResponse("Customer not found.",200,false);			
      }
  }

  public function Createcustomer(Request $request)
  {
		  $this->validate($request, [
    			'boxoffice_id'=>'required',
    			'email'=>'required',
    			'phone'=>'required',
    			'firstname'=>'required',
    			'lastname'=>'required',
    			'address'=>'required',
    			'email_verify'=>'nullable|in:Y,N',
          'image'=> 'nullable',
          'tags'=> 'nullable'
			]);
          
      $firstCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id,'email'=>$request->email])->first();
      if($firstCheck !== null)
		  {
			    return $this->sendResponse("System should not allow to enter duplicate Email on same Boxoffice.",200,false);
		  }
		  if($request->image)
      {
          $path = app()->basePath('public/customer-images/');
          $fileName_image = $this->singleImageUpload($path, $request->image);
   
      }else{
			    $fileName_image = 'default_customer.png';
		  }
      $customer = new EtCustomers;
      $time = strtotime(Carbon::now());
      $customer->unique_code = "cus".$time.rand(10,99)*rand(10,99);
      $customer->boxoffice_id = $request->boxoffice_id;
      $customer->email = $request->email;
      $customer->phone = $request->phone;
      $customer->firstname = $request->firstname;
      $customer->lastname = $request->lastname;
      $customer->address = $request->address;
      $customer->email_verify = $request->email_verify;
      $customer->image = $fileName_image;
      $customer->tags =  $request->tags;
			$result = $customer->save();
			if($result)			
			{					
				  return $this->sendResponse("Customer added successfully.");			
			}			
			else			
			{				
				  return $this->sendResponse("Sorry! something wrong.",200,false);			
			}
  }

  public function CustomerDelete(Request $request)
  {
	    $this->validate($request, [
		      'unique_code'=>'required'
		  ]);

	    $get_single_customer_info = EtCustomers::where(['unique_code'=>$request->unique_code])->first();
	
  		$customerImage = app()->basePath('public/customer-images/'.basename($get_single_customer_info->image));
  		if(basename($get_single_customer_info->image) !== "default_customer.png"){
  				if(file_exists($customerImage)) {
  					@unlink($customerImage);
  				}
  		}
  				
  		$result = EtCustomers::where('unique_code',$request->unique_code)->delete();		
  		if($result)
  		{
  			  return $this->sendResponse("Customer deleted sucessfully.");	
  		}
  		else
  		{
  			  return $this->sendResponse("Sorry! something wrong.",200,false);	
  		}
  }

  public function CustomerUpdate(Request $request)
  {
  		$this->validate($request, [			
    			'unique_code'=>'required',
    			'boxoffice_id'=>'required',
    			'email'=>'required',
    			'phone'=>'required',
    			'firstname'=>'required',
    			'lastname'=>'required',
    			'address'=>'required',
          'image'=> 'nullable',
          'tags' => 'required'
		  ]);
          
      $firstCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id,'email'=>$request->email])
      ->whereNotIn('unique_code',[$request->unique_code])
      ->first();
          
      if($firstCheck !== null)
		  {
			    return $this->sendResponse("System should not allow to enter duplicate Email on same Boxoffice.",200,false);
      }
          
      if($request->image)
      {
          $path = app()->basePath('public/customer-images/');
          $fileName_image = $this->singleImageUpload($path, $request->image);
          $get_single_customer_info = EtCustomers::where(['unique_code'=>$request->unique_code])->first();
          
			    $customerImage = app()->basePath('public/customer-images/'.basename($get_single_customer_info->image));
      		if(basename($get_single_customer_info->image) !== "default_customer.png"){
          		if(file_exists($customerImage)) {
              	@unlink($customerImage);
          		}
      		}
				
  				$result = EtCustomers::where('unique_code',$request->unique_code)->update([
      				'unique_code'=>$request->unique_code,
      				'boxoffice_id'=>$request->boxoffice_id,
      				'email'=>$request->email,
      				'phone'=>$request->phone,
      				'firstname'=>$request->firstname,
      				'lastname'=>$request->lastname,
      				'address'=>$request->address,
              'image'=>$fileName_image,
              'tags'=>$request->tags
			    ]);
      }
      else
      {
  				$result = EtCustomers::where('unique_code',$request->unique_code)->update([
      				'unique_code'=>$request->unique_code,
      				'boxoffice_id'=>$request->boxoffice_id,
      				'email'=>$request->email,
      				'phone'=>$request->phone,
      				'firstname'=>$request->firstname,
      				'lastname'=>$request->lastname,
      				'address'=>$request->address
  				]);
		  }
	
  		/*$result = EtCustomers::where('unique_code',$request->unique_code)->update([
  				'unique_code'=>$request->unique_code,
  				'boxoffice_id'=>$request->boxoffice_id,
  				'email'=>$request->email,
  				'phone'=>$request->phone,
  				'firstname'=>$request->firstname,
  				'lastname'=>$request->lastname,
  				'image'=>$fileName_image
  		]);*/ 
  		if(!empty($result))
  		{
  			return $this->sendResponse("Customer updated sucessfully.");	
  		}
  		else
  		{
  			return $this->sendResponse("Sorry! something wrong.",200,false);
  		}
	}

  public function exportCustomers(Request $request)
  {
      $this->validate($request, [
          'boxoffice_id'=>'required'
      ]);
        
      $result = EtCustomers::where('boxoffice_id',$request->boxoffice_id)->get();
      if(sizeof($result)>0)
      {
          return $this->sendResponse($result);
      }
      else
      {
          return $this->sendResponse("No customer found.",200,false);
      }
  }

  function csvToArray($filename = '')
  {
      if (!file_exists($filename) || !is_readable($filename))
      return false;
      //dd($filename);
      $header = null;
      $data = array();
      if (($handle = fopen($filename, 'r')) !== false)
      {
          while (($row = fgetcsv($handle, 1000, ',')) !== false)
          {
              if (!$header)
              {
                  $header = $row;
              }
              else
              {
                  $data[] = array_combine($header, $row);
              }
          }
          fclose($handle);
      }

      return $data;
  }

  public function importCustomers(Request $request)
  {
      $this->validate($request, [
          'file'=>'required',
          'boxoffice_id'=>'required'
      ]);

      $result = 0;
      
      $file = $request->file;

      $customerArr = $this->csvToArray($file);

      foreach ($customerArr as $customer) {

          $time = strtotime(Carbon::now());

          $unique_code = "cus".$time.rand(10,99)*rand(10,99);
          $boxoffice_id = $request->boxoffice_id;
          $email = $customer['email'];
          $phone = $customer['phone'];
          $firstname = $customer['firstname'];
          $lastname = $customer['lastname'];
          $address = $customer['address'];
          $email_verify = 'N';
          $image = 'default.jpg';

          $addedCustomer = EtCustomers::where(['email'=>$email,'boxoffice_id'=>$boxoffice_id])->first();

          if (empty($addedCustomer)) {
              $customer = new EtCustomers;
                
              $customer->unique_code = $unique_code;
              $customer->boxoffice_id = $boxoffice_id;
              $customer->email = $email;
              $customer->phone = $phone;
              $customer->firstname = $firstname;
              $customer->lastname = $lastname;
              $customer->address = $address;
              $customer->email_verify = $email_verify;
              $customer->image = $image;
          
              $result = $customer->save();
          }
      }
      
      if($result != 0)
      {
          return $this->sendResponse("Customers imported successfully.");
      }
      else
      {
          return $this->sendResponse("Sorry! something wrong.",200,false);
      }
  }
}
