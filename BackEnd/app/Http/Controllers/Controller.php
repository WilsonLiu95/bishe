<?php

namespace App\Http\Controllers;
use App\Model;
use App\Model\Admin;
use App\Model\Student;
use App\Model\Teacher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
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
	public function redirect($url,$isUrl,$msg){
				return response()->json([
				            "state" => 301,
				            "url" => $url,
				            "type"=> $isUrl ? "url" : "route"]);
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
