<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use App\Mail\SendVerificationMail;
use App\EtUsers;
use App\EtInviters;
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
				  return $this->sendResponse("Somthing went wrong.",200,false);			
			}
  }

  public function isLogin(Request $request)
  {  
	    $this->validate($request, [
  				'email' => 'required|min:3|max:255',
  				'password' => 'required'
		  ]);

      $user = EtUsers::where(['email' => $request->email])->first();
		  $member = EtInviters::where(['email_id'=>$request->email])->first();	
		  if(isset($user))
      {
    			if(Hash::check($request->password, $user->password))
    			{
      				if($user->email_verify == 'N')
      				{
      					  return $this->sendResponse("User is not verified.", 200, false);
      				}
      				if($user->status == 'N')
      				{
      					  return $this->sendResponse("User is not enable.", 200, false);
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
    					$authentication['type'] = "admin";  
				      return $this->sendResponse($authentication);
			    }
			    else 
			    {
    				  return $this->sendResponse("Email or password is wrong.", 200, false);
    			}
		  }
  		else if(isset($member))
  		{
  				if(Hash::check($request->password, $member->password))
      		{
      				if($member->status == 'P')
      				{
        					return $this->sendResponse("Member is not approved.", 200, false);
  					  }
  					  
      				if($member->status == 'R')
      				{
      					 return $this->sendResponse("Member rejected.", 200, false);
      				}

      				$token_string = hash("sha256", rand());  
      				$authentication = Api_auth::updateOrCreate(['user_id' => $member->unique_code],[
        					'user_id' => $member->unique_code,
        					'token' => $token_string,
        					'user_type' => $member->role,
              ]);
          		$authentication['firstname'] = "";
          		$authentication['lastname'] = "";
          		$authentication['email']   = $member->email;
          		$authentication['phone'] = "";
      				$authentication['image'] = "";
      				$authentication['type'] = "member";
      				$authentication['boxoffice_id'] = $member->boxoffice_id;
      				$authentication['permission'] = $member->permission;
      				$authentication['sub_permission'] = $member->sub_permission; 
				      return $this->sendResponse($authentication);
			    } 
			    else 
			    {						 
    				  return $this->sendResponse("Email or password is wrong.", 200, false);
    			}
		  }
		  else
		  {
			    return $this->sendResponse("Email or password is wrong.", 200, false);
		  }
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

  public function get_profile_data(Request $request)
  {
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
  			  return $this->sendResponse("Profile not found.",200,false);			
  		}
  }

	public function update_profile_data(Request $request)
	{
		  $this->validate($request, [			
			  	'id'=> 'required',
          		'unique_code' => 'required',
			    'firstname' => 'required',
    			'email' => 'required|email|unique:et_users,email,'.$request->id,
    			'description' => 'nullable',
    			'phone'  => 'required|numeric',
    			'image'=> 'nullable'
			]);

			if($request->image)
			{
					$path = app()->basePath('public/user-images/');
					$fileName_image = $this->singleImageUpload($path, $request->image);
					$get_single_user_info = EtUsers::where(['unique_code'=>$request->unique_code])->first();
					$userImage = app()->basePath('public/customer-images/'.basename($get_single_user_info->image));
					if(basename($get_single_user_info->image) !== "default.png"){
						if(file_exists($userImage)) {
							@unlink($userImage);
						}
					}
							
					$result = EtUsers::where('unique_code',$request->unique_code)->update([
    					'firstname'=>$request->firstname,
    					'email'=>$request->email,
    					'description'=>$request->description,
    					'phone'=>$request->phone,
    					'image'=>$fileName_image
					]);
			}
      else
			{
					$result = EtUsers::where('unique_code',$request->unique_code)->update([
  						'firstname'=>$request->firstname,
  						'email'=>$request->email,
  						'description'=>$request->description,
  						'phone'=>$request->phone
					]);
			}
		
  		if(!empty($result))
  		{
  			  return $this->sendResponse("Profile updated sucessfully.");	
  		}
  		else
  		{
  			  return $this->sendResponse("Something Went Wrong.",200,false);
  		}
  }

  public function checkToken(Request $request)
  {
  		$this->validate($request, [
    			'user_id'=>'required',
    			'user_type'=>'required|in:SA,A,EO,OM',
    			'token'=>'required'
  		]);

  	  $result = Api_auth::where(['user_id'=>$request->user_id,'user_type'=>$request->user_type,'token'=>$request->token])->first();
  		if($result === null)
  		{
  			  return $this->sendResponse("Token not match.",200,false);
  		}
  		else
  		{
  			  return $this->sendResponse("Token match success.");
  		}
  }

  public function userReLogin(Request $request)
	{
  		$this->validate($request, [
    			'user_id'=>'required',
    			'user_type'=>'required|in:SA,A,EO,OM',
    			'password'=>'required'
  		]);

      $user = EtUsers::where(['unique_code' => $request->user_id])->first();
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
              $authentication['email'] = $user->email;
              $authentication['phone'] = $user->phone;
              $authentication['image'] = $user->image;

				      return $this->sendResponse($authentication);
			    } 
			    else 
			    {						 
    				  return $this->sendResponse("user_id or password is wrong.", 200, false);
    			}
    	} 
      return $this->sendResponse("User id or password is wrong.", 200, false);
	}

	public function userImageRemove(Request $request)
	{
		  $this->validate($request, [
			    'unique_code'=>'required'
			]);
			
		  $get_boxoffice_info = EtUsers::where(['unique_code'=>$request->unique_code])->first();
			if(!empty($get_boxoffice_info))
			{
          $updateImage = 0;
					$userImage = app()->basePath('public/user-images/'.basename($get_boxoffice_info->image));
					if(basename($get_boxoffice_info->image) !== "default.png"){
						if(file_exists($userImage)) {
							@unlink($userImage);
							$updateImage = EtUsers::where(['unique_code'=>$request->unique_code])->update(['image'=>"default.png"]);
						}
					}

  				if($updateImage == 1)
  				{
  					  return $this->sendResponse("Image removed.");
  				}				
  				else
  				{
  					  return $this->sendResponse("Image already removed.",200,false);
  				}
			}
			else
			{
				  return $this->sendResponse("Sorry!something wrong.",200,false);
			}
	}
}