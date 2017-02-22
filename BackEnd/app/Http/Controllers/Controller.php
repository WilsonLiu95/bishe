<?php

namespace App\Http\Controllers;

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
    public $error, $redirect,$success;

    public function __construct(Request $request)
    {
        $this->redirect= array(
            "state" => 301,
            "url" => "",
            "type"=>"route",
        );
    }

    public function getSessionInfo($which){
        // which 即session中存储的基本用户信息,目前包括 id,type两个
        $session_which = session()->get($which);
        if (isset($session_which)){
            return $session_which;
        }else{
            $this->findUser();
            return session()->get($which);
        }
    }
    // 根据openid设置type与id,确保type与id确实存在于session中
    public function findUser(){
        $openid = session()->get("openid");

        $student = Student::where("openid",$openid);
        if ($student->count()){
            session()->put("type",2);
            session()->put("id",$student->get()[0]["id"]);
        }
        $teacher = Teacher::where("openid",$openid);
        if ($teacher->count()){
            session()->put("type",1);
            session()->put("id",$teacher->get()[0]["id"]);
        }
    }
    public function getUserClass(){
        $userType = array(
            0 => Admin::class,
            1 => Teacher::class,
            2 => Student::class,
        );
        // 查找user的类
        $type = $this->getSessionInfo("type");
        return $userType[$type];

    }
    public function getUser(){
        // 查找user对象
        $id = $this->getSessionInfo("id");
        $User = $this->getUserClass();
        return $User::find($id);
    }
    public function isTeacher(){
        return session()->get('type') == 1;
    }
    public function getGrade(){
        return $this->getUser()
            ->institute()->first()
            ->grade()->first();
    }
    public function json($state=1,$data){
        $res = array(
            "state"=>$state,
            "data"=>$data
        );
        return response()->json($res);
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
