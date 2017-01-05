<?php

namespace App\Http\Controllers;

use App\Model\Student;
use App\Model\Teacher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    public $error, $redirect;
    public $success;
//    public $userClass;

    public function __construct(Request $request)
    {
//        if(session()->get("type")){
//            $this->userClass = session()->get("type") == "student" ? Student::class : Teacher::class;
//        }
        $this->error = array(
            "state" => 0,
            "msg"=> "出错",
            "data"=> array()
        );
        $this->success = array(
            "state" => 1,
            "msg"=> "成功",
            "data"=> array()
        );
        $this->redirect= array(
            "state" => 301,
            "url" => "",
            "type"=>"route",
        );

    }
}
