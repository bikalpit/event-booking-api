<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function sendResponse($message, int $responseCode = 200, bool $data = true)
    {
        //dd("aaa");
        return response()->json(["data" =>$data, "response" => $message], $responseCode); 
    }

    public function uploadFiles($location, $fileInput)
    {
        $encryptedName = md5(rand().time());
        $imageName = $encryptedName.'.'.$fileInput->getClientOriginalExtension();
        $fileInput->move($location,$imageName);
        return $imageName;
    }

    public function singleImageUpload($myPath,$image)
    {
        $folderPath = $myPath;//app()->basePath('public/staff-images/');
        $fileName =  rand().'Image.png';
        $base64Image = $image;
        $base64Image = trim($base64Image);
        $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);

        $imageData = base64_decode($base64Image);
        $filePath = $folderPath . $fileName;
        if(file_put_contents($filePath, $imageData)){
            $finalImage = $fileName; 
        }
        else
        {
            $finalImage = "default.png";
        } 
        
        return $finalImage;
    }

    public function imageUpload($myPath,$image)
    {
        $finalImage = [];
        $folderPath = $myPath;//app()->basePath('public/staff-images/');
        foreach($image as $key=> $new_image)
        {
            $fileName =  rand().'Image.png';
            $base64Image = $new_image;
            $base64Image = trim($base64Image);
            $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
            $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
            $base64Image = str_replace(' ', '+', $base64Image);

            $imageData = base64_decode($base64Image);
            $filePath = $folderPath . $fileName;
            if(file_put_contents($filePath, $imageData)){
                $finalImage[] = $fileName; 
            } 
        }
        return $finalImage;
    }
}
