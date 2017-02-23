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
            ->institute
            ->grade()
            ->where('status',1) // 目前生效的年级
            ->first();
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

    public function checkOneStudent($id){
        $sc = Model\Schedule::where("student_id",$id)->get(); // 获取所有相关的进度信息
        $sc->each(function($item) {
            $this->checkOneSchdule($item->id);
        });
    }

    public function checkOneSchdule($id){
        $item = Model\Schedule::find($id);
        // 首先看有无该课程
        if(!$item->course()->exists() ){
            $item->delete();
            return true;
        }
        $course = $item->course()->first();
        // 有该课程情况下
//            var_dump($item->course()->first()['status']);
        if($course->status == 0){
            if($course->check_status!=2){
                // 未通过审核情况下删除,此时不可能有人选中过
                $item->delete(); // 审核中的课程不可能被选择
            }
            if($item->status != 0 ){
                $item->delete(); // 课程被删除的情况下,不可能还有人在 "互选中"or"完成互选"
            }
        }
        if($course->status == 1){
            $item->delete(); // 审核中的课程不可能被选择
        }
        if($course->status == 2){
            if($item->status == 2){
                $item->delete(); // 互选中的课程,不可能有人已经变成"完成互选"
            }
        }
        if($course->status == 3){
            if($item->status == 1){
                $item->delete(); // 完成互选的课程,不可能有人还在互选中
            }
        }
    }
    public  function checkOneTeacher($id){
        // 课程信息必须一致保持一致,即使出错也只能恢复到待审核状态,不可直接删除
        $course_list = Model\Course::where("teacher_id",$id)->get();
        $course_list->each(function($item){
            $this->checkOneCourse($item->id);
        });

    }
    public function checkOneCourse($id){
        $item = Model\Course::find($id);
        if($item->status == 0){ // 课程已被删除,则将所有选课的状态恢复到退选
            $item->schedule()->whereIn("status",[1,2])
                ->update(['status'=>0]);
        }
        if($item->status == 1){
            // 如果是还在审核的课程,则不可能被选上
            $item->schedule()->update(["status"=>0]);
            if($item->check_status == 2){
                $item->update(['check_status'=>1]); // 课程处于审核中状态下,不可能审核状态变为"通过审核"
            }
        }
        if($item->status == 2){ // 课程处于互选中
            $item->schedule()->where("status",2)
                ->update(["status"=>1]); // 互选中的课程如果有人已经选上,则更正为"选定中"
        }
        if($item->status == 3){ // 课程已完成互选
            if($item->schedule()->where("status",2)->count() == 1){
                // 如果存在互选人,且胡选人为一则情况正常
                $item->schedule()->where("status",1)
                    ->update(["status"=>0]); // 已完成互选的课程,将所有"选定中"的人改为退选
            }else if($item->schedule()->where("status",2)->count() == 0){
                // 不存在胡选人,则将课程恢复到互选中
                $item->update(['status'=>2]);
            }else{
                // 人数>1,出错,将所有人及课程恢复到 互选中
                $item->update(['status'=>2]);
                $item->schedule()->where("status",2)
                    ->update(["status"=>1]);

            }
        }
    }
}
