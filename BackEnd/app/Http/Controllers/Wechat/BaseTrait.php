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
        return $this->getUser()
            ->institute
            ->grade()
            ->where('status',1) // 		目前生效的年级
            ->first();
    }

    public function checkOneStudent($id){
        $sc = Model\Schedule::where("student_id",$id);
        // 		获取所有相关的进度信息
        $sc->get()->each(function($item) {
            // 该学生的 单个进度与单个课程进行校验 保证数据平稳退化
            $this->checkOneSchdule($item->id);
        });
        // 去除单门课程异常后,整体考虑一遍
        // 如果该学生有进度是已完成互选,则将其他互选中进度清空
        if($sc->where("status",2)->exists()){
            Model\Schedule::where("student_id",$id) // TODO: 不理解 未使用$sc 使用SC将造成无法成功更新
            ->where("status",1)->update(['status'=>0]);

        }
    }

    public function checkOneSchdule($id){
        $item = Model\Schedule::find($id);
        // 		首先看有无该课程
        if(!$item->course()->exists() ){
            $item->delete();
            return true;
        }
        $course = $item->course()->first();
        // 		有该课程情况下
        if($course->status == 0){
            if($course->check_status!=2){
                // 				未通过审核情况下删除,此时不可能有人选中过
                $item->delete();
                // 				审核中的课程不可能被选择
            }
            if($item->status != 0 ){
                $item->delete();
                // 				课程被删除的情况下,不可能还有人在 "互选中"or"完成互选"
            }
        }
        if($course->status == 1){
            $item->delete();
            // 			审核中的课程不可能被选择
        }
        if($course->status == 2){
            if($item->status == 2){
                $item->delete();
                // 				互选中的课程,不可能有人已经变成"完成互选"
            }
        }
        if($course->status == 3){
            if($item->status == 1){
                $item->delete();
                // 				完成互选的课程,不可能有人还在互选中
            }
        }
    }
    public  function checkOneTeacher($id){
        // 	课程信息必须一致保持一致,即使出错也只能恢复到待审核状态,不可直接删除
        $course_list = Model\Course::where("teacher_id",$id)->get();
        $course_list->each(function($item){
            // 一一校对老师的课程数据
            $this->checkOneCourse($item->id);
        });

    }
    public function checkOneCourse($id){
        $item = Model\Course::find($id);
        if($item->status == 0){
            // 			课程已被删除,则将所有选课的状态恢复到退选
            $item->schedule()->whereIn("status",[1,2])
                ->update(['status'=>0]);
        }
        if($item->status == 1){
            // 			如果是还在审核的课程,则不可能被选上
            $item->schedule()->update(["status"=>0]);
            if($item->check_status == 2){
                $item->update(['check_status'=>1]);
                // 				课程处于审核中状态下,不可能审核状态变为"通过审核"
            }
        }
        if($item->status == 2){
            // 			课程处于互选中
            $item->schedule()->where("status",2)
                ->update(["status"=>1]);
            // 			互选中的课程如果有人已经选上,则更正为"选定中"
        }
        if($item->status == 3){
            // 			课程已完成互选
            if($item->schedule()->where("status",2)->count() == 1){
                // 				如果存在互选人,且胡选人为一则情况正常
                $item->schedule()->where("status",1)
                    ->update(["status"=>0]);
                // 				已完成互选的课程,将所有"选定中"的人改为退选
            }
            else if($item->schedule()->where("status",2)->count() == 0){
                // 				不存在胡选人,则将课程恢复到互选中
                $item->update(['status'=>2]);
            }
            else{
                // 				人数>1,出错,将所有人及课程恢复到 互选中
                $item->update(['status'=>2]);
                $item->schedule()->where("status",2)
                    ->update(["status"=>1]);

            }
        }
    }

}