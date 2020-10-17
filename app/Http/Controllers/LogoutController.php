<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Api_auth;
use App\Customer;
use Auth;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function isLogout(Request $request)
    {
        $user_token_data = Auth::user();
        
        $result = Api_auth::where('id', $user_token_data->id)->delete();
        if($result)
        {	
            return $this->sendResponse("You logout successfully.");
        } 
        else 
        {				
            return $this->sendResponse("Something went wrong.",200,false);
        }
    }
}    
