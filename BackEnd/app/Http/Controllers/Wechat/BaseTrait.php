<?php
namespace App\Http\Controllers\Wechat;
use App\Model;
use App\Model\Student;
use App\Model\Teacher;

trait BaseTrait {
    public function getSessionInfo($which){
        // 		which 即session中存储的基本用户信息,目前包括 id,type两个
        $session_which = session()->get($which);
        if (isset($session_which)){
            return $session_which;
        }
        else{
            $this->findUser();
            return session()->get($which);
        }
    }
    // 	根据openid设置type与id,确保type与id确实存在于session中
    private function findUser(){
        // openid一定存在,否则会被鉴权挡回去做微信认证
        $openid = session()->get("openid");

        $student = Student::where("openid",$openid);
        if ($student->exists()){
            session()->put("isTeacher",0);
            session()->put("id",$student->first()["id"]);
        }
        $teacher = Teacher::where("openid",$openid);
        if ($teacher->exists()){
            session()->put("isTeacher",1);
            session()->put("id",$teacher->first()["id"]);
        }
    }
    public function getUserClass(){
        // 		查找user的类
        return $this->isTeacher() ? Teacher::class : Student::class;
    }
    public function getUser(){
        // 		查找user对象
        $id = $this->getSessionInfo("id");
        $user = $this->getUserClass();
        return $user::find($id);
    }
    public function isTeacher(){
        return $this->getSessionInfo('isTeacher');
    }
    public function getGrade(){
        // 采用软删除，同时只有一个年份
        return $this->getUser()
            ->institute
            ->grade()
            ->first();
    }
}