<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($type=true, $data=[], $msg = '', $code = 200)
    {
    	$response = [
            'success' => $type,
            'data'    => $data,
            'message' => $msg,
            'code'    => $code,
        ];
        return response()->json($response, $code);
    }
}