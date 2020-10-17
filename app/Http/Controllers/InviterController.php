<?php

namespace App\Http\Controllers;
use App\EtInviters;
use App\EtBoxOffice;
use App\EtUsers;
use App\EtCustomers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Mailer;
use App\Mail\InviteMail;
use App\Mail\AcceptInviteMail;
use Carbon\Carbon;
class InviterController extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function get_inviters_data(Request $request)
    {
        $this->validate($request, [
  			    'boxoffice_id'=>'required',
            'status'=>'required|in:P,APR,R'
  			]);

        if ($request->status == 'P') {
          $get_inviters_info = EtInviters::where(['boxoffice_id'=>$request->boxoffice_id,'status'=>'P'])->get();
        }elseif ($request->status == 'APR') {
          $get_inviters_info = EtInviters::where(['boxoffice_id'=>$request->boxoffice_id,'status'=>'APR'])->get();
        }else{
          $get_inviters_info = EtInviters::where(['boxoffice_id'=>$request->boxoffice_id,'status'=>'R'])->get();
        }
        
        if(count($get_inviters_info)>0)
        {
            return $this->sendResponse($get_inviters_info);
        }
        else
        {
            return $this->sendResponse("Inviters not found.",200,false);
        }
    }

    public function Createinviters(Request $request)
  	{
    		$this->validate($request, [
      			'boxoffice_id'=>'required',
      			'email_id'=>'required',
      			'role'=>'required|in:EO,A,OM,OV',
      			'permission'=>'required|in:A,EM,OM,OV',
      			'sub_permission'=>'nullable'
    		]);
          
        $firstCheck = EtInviters::where(['boxoffice_id'=>$request->boxoffice_id,'email_id'=>$request->email_id])->first();
        $secondCheck = EtUsers::where('email',$request->email_id)->first();
        $thirdCheck = EtCustomers::where('email',$request->email_id)->first();
        
        if($firstCheck !== null)
  			{
  				  return $this->sendResponse("System should not allow to enter duplicate Inviter name for one admin.",200,false);
        }
            
        if($secondCheck !== null)
  			{
  				  return $this->sendResponse("Sorry! Email already used, Please try another.",200,false);
        }

        if($thirdCheck !== null)
        {
            return $this->sendResponse("Sorry! Email already used, Please try another.",200,false);
        }

        $boxoffice = EtBoxOffice::where('unique_code',$request->boxoffice_id)->first();

        $inviters = new EtInviters;
        $time = strtotime(Carbon::now());
        $inviters->unique_code = "inv".$time.rand(10,99)*rand(10,99);
        $inviters->boxoffice_id = $request->boxoffice_id;
        $inviters->name = "Your Name";
        $inviters->email_id = $request->email_id;
        $inviters->status = "P";
        $inviters->image = "default.png";
        $inviters->invite_datetime = date('Y-m-d H:i:s');
        $inviters->verify_token =  "v_token".$time.rand(10,99)*rand(10,99);
        $inviters->role = $request->role;
        $inviters->permission = $request->permission;
        $inviters->sub_permission = $request->sub_permission;
  			$result = $inviters->save();

  			if($result)
  			{
            $this->configSMTP();
            $data = ['name'=>$boxoffice->box_office_name,'token'=>$inviters->verify_token];
            Mail::to($request->email_id)->send(new InviteMail($data));
            if (Mail::failures()) {
                return $this->sendResponse("Sorry! Something wrong, Mail not sent.",200,false);
            }
            else
            {
                return $this->sendResponse("Inviter added successfully.");
            }
  			}
  			else
  			{
  				  return $this->sendResponse("Sorry! Something Wrong.",200,false);
  			}
    }

    public function invitationAnswer($token,$ans)
    {   
        $inviter = EtInviters::where('verify_token', $token)->first();
        if ($inviter->status == 'P') {
            if ($ans == 'accept') {
                $boxoffice = EtBoxOffice::where('unique_code',$inviter->boxoffice_id)->first();
                $password = substr( str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 10);
                $securePass = Hash::make($password);
                $this->configSMTP();
                $update_pass = EtInviters::where('verify_token', $token)->update(['password'=>$securePass,'status'=>'APR']);
                if ($update_pass) {
                    $data = ['password'=>$password,'name'=>$boxoffice->box_office_name];
                    Mail::to($inviter->email_id)->send(new AcceptInviteMail($data));
                    if (Mail::failures()) {
                        return $this->sendResponse("Sorry! Something wrong, Mail not sent.",200,false);
                    }
                }else{
                    return $this->sendResponse("Sorry! Something went wrong.",200,false);
                }
                return redirect('https://api.eventjio.com/api/thank-you');
            }else{
                $update_pass = EtInviters::where('verify_token', $token)->update(['status'=>'R']);

                return redirect('https://api.eventjio.com/api/reject-invitation');
            }
        }elseif ($inviter->status == 'APR') {
            return redirect('https://api.eventjio.com/api/already-accepted');
        }else{
            return redirect('https://api.eventjio.com/api/already-rejected');
        }
        
    }

    public function thankyouPage()
    {
        return view('thank-you');
    }

    public function rejectInvitation()
    {
        return view('reject-invitation');
    }

    public function alreadyAccepted()
    {
        return view('already-accepted');
    }

    public function alreadyRejected()
    {
        return view('already-rejected');
    }

    public function InviterDelete(Request $request)
  	{
  		  $this->validate($request, [
  			    'unique_code'=>'required'
  			]);
  				
    		$result = EtInviters::where('unique_code',$request->unique_code)->delete();
    		if($result)
    		{
    			  return $this->sendResponse("Inviter deleted sucessfully.");
    		}
    		else
    		{
    			  return $this->sendResponse("Sorry! Something wrong.",200,false);
    		}
  	}

    public function resendInvitation(Request $request)
    {
        $this->validate($request, [
            'invitation_id'=>'required' 
        ]);

        $invitation = EtInviters::where('unique_code',$request->invitation_id)->first();

        if ($invitation->status == 'P') {
            return $this->sendResponse("This invitation is still pending.",200,false);
        }elseif ($invitation->status == 'APR') {
            return $this->sendResponse("This invitation is already approved.",200,false);
        }elseif ($invitation->status == 'R') {
            $time = strtotime(Carbon::now());
            $boxoffice = EtBoxOffice::where('unique_code',$invitation->boxoffice_id)->first();
            $verify_token =  "v_token".$time.rand(10,99)*rand(10,99);
            $update_token = EtInviters::where('unique_code', $request->invitation_id)->update(['verify_token'=>$verify_token]);
            if ($update_token) {
                $this->configSMTP();
                $data = ['name'=>$boxoffice->box_office_name,'token'=>$verify_token];
                Mail::to($invitation->email_id)->send(new InviteMail($data));
                if (Mail::failures()) {
                    return $this->sendResponse("Sorry! Something wrong, Mail not sent.",200,false);
                }
                else
                {
                    $update_status = EtInviters::where('unique_code', $request->invitation_id)->update(['status'=>'P']);
                    if ($update_status) {
                        return $this->sendResponse("Inviter added successfully.");
                    }else{
                        return $this->sendResponse("Sorry! Something wrong.",200,false);
                    }
                }
            }
        }
    }
}
