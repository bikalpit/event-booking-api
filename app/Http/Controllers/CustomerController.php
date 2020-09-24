<?php

namespace App\Http\Controllers;
use App\EtCustomers;
use Illuminate\Http\Request;
use Carbon\Carbon;
class CustomerController extends Controller
{ 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // testing
    }
    
    function get_single_customer_data(Request $request){
        $this->validate($request, [
			'unique_code'=>'required'
			]);
        $get_single_customer_info = EtCustomers::where(['unique_code'=>$request->unique_code])->get();
        
        if(count($get_single_customer_info)>0)		
        {					
            return $this->sendResponse($get_single_customer_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
        }
    }

    function get_all_customer_data(Request $request){
        $this->validate($request, [
			'boxoffice_id'=>'required'
			]);
        $get_all_customer_info = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id])->get();
        
        if(count($get_all_customer_info)>0)		
        {					
            return $this->sendResponse($get_all_customer_info);			
        }			
        else			
        {				
            return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
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
			'email_verify'=>'required|in:Y,N',
			'image'=>'required'
			]);
			
        
            $firstCheck = EtCustomers::where(['boxoffice_id'=>$request->boxoffice_id,'email'=>$request->email])->first();
            if($firstCheck !== null)
			{
				return $this->sendResponse("System should not allow to enter duplicate Email on same Boxoffice.",200,false);
			}
            $customer = new EtCustomers;
            $time = strtotime(Carbon::now());
			$customer->unique_code = "cus".$time.rand(10,99)*rand(10,99);
			$customer->boxoffice_id = $request->boxoffice_id;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->firstname = $request->firstname;
            $customer->lastname = $request->lastname;
            $customer->email_verify = $request->email_verify;
            $customer->image = $request->image;
            
	
			$result = $customer->save();
			if($result)			
			{					
				return $this->sendResponse("Customer Added Successfully");			
			}			
			else			
			{				
				return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
			
			
    }


    public function CustomerDelete(Request $request)
	{
		$this->validate($request, [
			'unique_code'=>'required'
			]);
       
        
				
				
		$result = EtCustomers::where('unique_code',$request->unique_code)->delete();		
		if($result)
		{
			return $this->sendResponse("Customer Deleted Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something went wrong.",200,false);	
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
			'email_verify'=>'required|in:Y,N',
			'image'=>'required'
			]);

		
		$result = EtCustomers::where('unique_code',$request->unique_code)->update([
				'unique_code'=>$request->unique_code,
				'boxoffice_id'=>$request->boxoffice_id,
				'email'=>$request->email,
				'phone'=>$request->phone,
				'firstname'=>$request->firstname,
				'lastname'=>$request->lastname,
				'email_verify'=>$request->email_verify,
				'image'=>$request->image
				]);
		if(!empty($result))
		{
			return $this->sendResponse("Customer Updated Sucessfully");	
		}
		else
		{
			return $this->sendResponse("Something Went Wrong.",200,false);
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
          return $this->sendResponse("Something went wrong.",200,false);
      }
  }
}
