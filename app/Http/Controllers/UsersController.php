<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use App\Mail\SendVerificationMail;
use App\EtUsers;
use App\Api_auth;
use Carbon\Carbon;
use DB;

class UsersController extends Controller
{
  public function adminSignup(Request $request)
  {
      $this->validate($request, [			
    			'firstname' => 'required',
          'password' => 'required',
    			'email' => 'required|email|unique:et_users',
    			'description' => 'nullable'
      ]); 

      $time = strtotime(Carbon::now());
      $unique_code = "usr".$time.rand(10,99)*rand(10,99);
      $password = Hash::make($request->password);
      $user = new EtUsers;
      $user->email = $request->email;
			$user->password = $password;
			$user->firstname = $request->firstname;
			$user->unique_code = $unique_code;
			$user->description = $request->description;
      $user->role = "A";
      $user->permission = "A";
      $user->status  = "Y";
      $user->email_verify = "Y";
      $user->image = "default.png";
			$result = $user->save();						
			if($result)			
			{					
				return $this->sendResponse("Registration successfully done.");			
			}			
			else			
			{				
				return $this->sendResponse("somthing went wrong",200,false);			
			}
  }

  public function isLogin(Request $request)
  {  
	    $this->validate($request, [
  				'email' => 'required|min:3|max:255',
  				'password' => 'required'
		  ]);
      $user = EtUsers::where(['email' => $request->email])->first();

		  if(isset($user))
      {
    			if(Hash::check($request->password, $user->password))
    			{
      				if($user->email_verify == 'N')
      				{
      					 return $this->sendResponse("User is not verified", 200, false);
      				}
      				if($user->status == 'N')
      				{
      					 return $this->sendResponse("User is not enable", 200, false);
      				}

      				$token_string = hash("sha256", rand());  
      				$authentication = Api_auth::updateOrCreate(['user_id' => $user->unique_code],[
        					'user_id' => $user->unique_code,
        					'token' => $token_string,
        					'user_type' => $user->role,
              ]);

              $authentication['firstname'] = $user->firstname;
              $authentication['lastname'] = $user->lastname;
              $authentication['email']   = $user->email;
              $authentication['phone'] = $user->phone;
              $authentication['image'] = $user->image;

				      return $this->sendResponse($authentication);
			    } 
			    else 
			    {						 
    				  return $this->sendResponse("email or password is wrong", 200, false);
    			}

    	} 
      return $this->sendResponse("email or password is wrong", 200, false);
  }

  public function demoTest(Request $request)
  {
	    $this->validate($request, [			
    			'firstname' => 'required',
    			'password' => 'required',
    			'email' => 'required|email|unique:et_users',
    			'description' => 'nullable',
    			'phone'  => 'required|numeric',
    			'zipcode'=> 'required|min:5|max:6'
		  ]); 
  }

  public function sendVerificationEmail(Request $request)
  {
      $this->validate($request, [
          'email' => 'required|email'
      ]);

      $userInfo = EtUsers::where('email', $request->email)->first();
      $name = $userInfo->firstname.' '.$userInfo->lastname;
      $email = $request->email;
      $verification_token = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 20 );
      EtUsers::where('email', $request->email)->update(['email_verification_token'=>$verification_token]);
      $data = ['name'=>$name, 'verification_token'=>$verification_token];
      try{
          Mail::to($email)->send(new SendVerificationMail($data));  
      }catch(\Exception $e){
          $msg = $e->getMessage();
          return $this->sendResponse($msg,200,false);
      }
          return $this->sendResponse("Mail sent successfully.");
  }

  public function verifyEmail($verification_token)
  {
      EtUsers::where('email_verification_token', $verification_token)->update(['email_verify'=>'Y']);
      return view('verify-email');
  }

  public function checkEmail(Request $request)
  {
      $this->validate($request, [
          'email' => 'required|email'
      ]);

      $allUsers = EtUsers::where('email',$request->email)->first();

      if ($allUsers) {
        return $this->sendResponse("Email already exist.", 200, false);
      }else{
        return $this->sendResponse("Email is available.");
      }
  }

  public function getTimezones(Request $request)
  {
      $timezones = DB::table('et_timezone')->get();
      return $this->sendResponse($timezones);
  }

  public function getLanguages(Request $request)
  {
      $languages = DB::table('languages')->get();
      return $this->sendResponse($languages);
  }

  function get_profile_data(Request $request){
    $this->validate($request, [
  'unique_code'=>'required'
  ]);
    $get_profile_info = EtUsers::where(['unique_code'=>$request->unique_code])->get();
    
    if(count($get_profile_info)>0)		
    {					
        return $this->sendResponse($get_profile_info);			
    }			
    else			
    {				
        return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
    }
  }   
} 