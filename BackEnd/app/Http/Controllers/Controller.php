<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;

	public function json($state=1,$data=array()){
		$res = array(
		            "state"=>$state,
		            "data"=>$data
		        );
		return response()->json($res);
	}
	public function redirect($option=array(), $msg="",$data = array()){
		// 如果要填路径则$option为路径
				return response()->json([
				            "state" => 301,
				            "option"=>$option,
                            'msg'=>$msg,
							'data'=> $data]);
	}
	public function toast($state=1,$msg="",$data=array()){
		$toast = array(
		            "state"=>$state,
		            "msg"=>$msg,
		            "data"=>$data,
		
		);
		return response()->json($toast);
	}
	
}
