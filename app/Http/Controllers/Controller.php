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
}
