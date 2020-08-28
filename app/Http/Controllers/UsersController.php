<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\EtUsers;
use App\Api_auth;
use Carbon\Carbon;

class UsersController extends Controller
{
  public function adminSignup(Request $request)
  {
    $this->validate($request, [			
			'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required',
			'phone' => 'required|digits:10|numeric',
			'email' => 'required|email|unique:et_users'
     ]); 
            
            $time = strtotime(Carbon::now());
            $unique_code = "usr".$time.rand(10,99)*rand(10,99);
            $password = Hash::make($request->password);
			$user = new EtUsers;
            $user->email = $request->email;
			$user->password = $password;
			$user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->unique_code = $unique_code;
			$user->phone = $request->phone;
            $user->role = "A";
            $user->permission = "A";
            $user->status  = "Y";
            $user->email_verify = "N";
            $user->image = "default.png";
			$result = $user->save();						
			if($result)			
			{					
				return $this->sendResponse("Registration successfully done");			
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
		if(isset($user))		{
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
  public function logout(Request $request)
  {
      dd("Hello world");
  }
}    
