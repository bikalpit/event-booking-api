<?php

namespace App\Http\Controllers;
use App\EtBoxOffice;
use Illuminate\Http\Request;
use Carbon\Carbon;
class BoxOfficeController extends Controller
{
  public function __construct()
  {
     
  }

  public function CreateBoxOffice(Request $request)
  {
      $this->validate($request, [
          'box_office_name'=>'required',
          'admin_id'=>'required',
          'type'=>'required',
          'country'=>'required|numeric',
          'currency'=>'required|numeric',
          'genre'=>'required',
          'genre_type'=>'required'
      ]);

      $firstCheck = EtBoxOffice::where(['admin_id'=>$request->admin_id,'box_office_name'=>$request->box_office_name])->first();
      if($firstCheck !== null)
  		{
  			  return $this->sendResponse("System should not allow to enter duplicate BoxOffice name for one business.",200,false);
  		}

      $boxoffice = new EtBoxOffice;
      $time = strtotime(Carbon::now());
		  $boxoffice->unique_code = "box".$time.rand(10,99)*rand(10,99);
		  $boxoffice->box_office_name = $request->box_office_name;
      $boxoffice->admin_id = $request->admin_id;
      $boxoffice->type    = $request->type;
      $boxoffice->country = $request->country;
      $boxoffice->currency= $request->currency;
      $boxoffice->genre   = $request->genre;
      $boxoffice->genre_type = $request->genre_type;
      $boxoffice->box_office_link = $boxoffice->unique_code;
      $boxoffice->email_order_notification = "N";
      $boxoffice->account_owner = "N";
      $boxoffice->hide_tailor_logo = "N";
			$boxoffice->image  = "default_image.png";
			$result = $boxoffice->save();

			if($result)			
			{					
				  return $this->sendResponse("BoxOffice Added Successfully.");			
			}			
			else			
			{				
				  return $this->sendResponse("Sorry! Somthing Wrong",200,false);			
			}
  }

  public function get_single_boxoffice_data(Request $request)
  {
      $this->validate($request, [
			    'unique_code'=>'required'
			]);

      $get_boxoffice_info = EtBoxOffice::where(['unique_code'=>$request->unique_code])->get();

      if(count($get_boxoffice_info)>0)		
      {					
          return $this->sendResponse($get_boxoffice_info);			
      }			
      else			
      {				
          return $this->sendResponse("Box Office not found.",200,false);			
      }
  }

  public function get_all_boxoffice_data(Request $request)
  {
      $this->validate($request, [
		      'admin_id'=>'required'
		  ]);

      $get_boxoffice_info = EtBoxOffice::where(['admin_id'=>$request->admin_id])->get();

      if(count($get_boxoffice_info)>0)		
      {					
          return $this->sendResponse($get_boxoffice_info);			
      }			
      else			
      {				
          return $this->sendResponse("Box Office not found.",200,false);			
      }
  }

  public function BoxOfficeDelete(Request $request)
  {
  		$this->validate($request, [
  			  'unique_code'=>'required'
  		]);

    	$result = EtBoxOffice::where('unique_code',$request->unique_code)->delete();

    	if($result)
    	{
    		return $this->sendResponse("Box Office Deleted Sucessfully.");	
    	}
    	else
    	{
    		return $this->sendResponse("Something went wrong.",200,false);	
    	}
  }

  public function BoxOfficeUpdate(Request $request)
  {
	    $this->validate($request, [
        	'unique_code'=>'required',
        	'box_office_name'=>'required',
        	'type'=>'required',
        	'country'=>'nullable|numeric',
    			'currency'=>'nullable|numeric',
    			'genre'=>'required',
    			'genre_type'=>'required',
    			'account_owner'=>'required|in:N,Y',
    			'add_email'=>'nullable',
    			'box_office_link'=>'required',
    			'email_order_notification'=>'required|in:N,Y',
    			'hide_tailor_logo'=>'required|in:N,Y',
    			'language'=>'nullable',
    			'timezone'=>'nullable',
    			'image'=>'nullable'
      ]);
	 
      $boxoffices = EtBoxOffice::whereNotIn('unique_code',[$request->unique_code])->get();
      foreach ($boxoffices as $boxoffice) {
        if ($boxoffice->box_office_link == $request->box_office_link) {
          return $this->sendResponse("Box office link already exist.",200,false);
        }
      }

  		if(!empty($request->image))
  		{
    			$path = app()->basePath('public/boxoffice-images/');
    			$fileName_image = $this->singleImageUpload($path, $request->image);

				  /*$get_single_user_info = EtUsers::where(['unique_code'=>$request->unique_code])->first();

  				$userImage = app()->basePath('public/customer-images/'.basename($get_single_user_info->image));
  				if(basename($get_single_user_info->image) !== "default.png"){
  					if(file_exists($userImage)) {
  						@unlink($userImage);
  					}
  				}*/
				 $result = EtBoxOffice::where('unique_code',$request->unique_code)->update(['image'=>$fileName_image]);
    	}
    	else
    	{
    			$result = EtBoxOffice::where('unique_code',$request->unique_code)->update([
              'box_office_name'=>$request->box_office_name,
        			'type'=>$request->box_office_name,
        			'country'=>$request->country,
        			'currency'=>$request->currency,
        			'genre'=>$request->genre,
              'genre_type'=>$request->genre_type,
              'account_owner'=>$request->account_owner,
              'add_email'=>$request->add_email,
              'box_office_link'=>$request->box_office_link,
              'email_order_notification'=>$request->email_order_notification,
              'hide_tailor_logo'=>$request->hide_tailor_logo,
              'language'=>$request->language,
              'timezone'=>$request->timezone
    			]);
  	  }

  		if($result)
  		{
  			  return $this->sendResponse("Box Office Updated Sucessfully.");	
  		}
  		else
  		{
  			  return $this->sendResponse("Something Went Wrong.",200,false);
  		}
  }

	public function removeImage(Request $request)
	{
  		$this->validate($request, [
  			  'unique_code'=>'required'
  		]);
		
	    $get_boxoffice_info = EtBoxOffice::where(['unique_code'=>$request->unique_code])->first();
      
		  if(!empty($get_boxoffice_info))
		  { 
          $updateImage = 0;
				  $userImage = app()->basePath('public/boxoffice-images/'.basename($get_boxoffice_info->image));
					if(basename($get_boxoffice_info->image) !== "default_image.png"){
						if(file_exists($userImage)) {
							@unlink($userImage);
							$updateImage = EtBoxOffice::where(['unique_code'=>$request->unique_code])->update(['image'=>"default_image.png"]);
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
				  return $this->sendResponse("Sorry! Something wrong.",200,false);
			}
  }
}