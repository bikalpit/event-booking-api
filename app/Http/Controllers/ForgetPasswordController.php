<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use App\Mail\ForgotEmail;
use App\EtUserToken;
use App\EtUsers;

class ForgetPasswordController extends Controller
{
    public function sendForgotEmail(Request $request)
    {   
        $this->validate($request, [
          'email' => 'required|email',
          'url'=> 'required'
        ]);

        $userResult = EtUsers::where('email',$request->email)->first();
        if(empty($userResult))
        {
            return $this->sendResponse("Your email not registered.",200,false);
        }
        else
        {
            $Random_Token = md5(substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 10 ));
            $result = EtUserToken::updateOrCreate(
                ['email' => $request->email],
                ['token' => $Random_Token]
            );
           $this->configSMTP();
            $data = ['token'=>$result->token,'name'=>$userResult->firstname.' '.$userResult->lastname,'url'=>$request->url];
            Mail::to($request->email)->send(new ForgotEmail($data));

            if (Mail::failures()) {
                return $this->sendResponse("Sorry! something wrong try again.",200,false);
            }
            else
            {
                return $this->sendResponse("Mail send for reset password.");
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'token'   =>'required',
            'password'=>'required'
        ]);
        
        $password = Hash::make($request->password);
        $getResult = EtUserToken::where(['token'=>$request->token])->first();
        if($getResult)
        {
            $getUpdate = EtUsers::where(['email'=>$getResult->email])->update(['password'=>$password]);
            if($getUpdate == 1)
            {
                return $this->sendResponse("Password reset success.");
            }
            else
            {
                return $this->sendResponse("Sorry! something wrong.",200,false);
            }
        }
        else
        {
            return $this->sendResponse("Token not avilable.",200,false);
        }
    }
}