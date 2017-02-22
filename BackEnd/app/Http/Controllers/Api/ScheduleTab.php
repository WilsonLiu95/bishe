<?php

namespace App\Http\Controllers\Api;


use App\Model\Course;
use App\Model\Schedule;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ScheduleTab extends Controller
{
    public function getIndex(){
        $res = array();
        $id = $this->getSessionInfo("id");

        $grade = $this->getGrade();
        if($this->isTeacher()){
            $this->checkCourseData($id);

            $course = Course::where("teacher_id",$id)
                ->where("status","!=",0)
                ->orderBy("status")
                ->get();
            $course->each(function($item){
               $item->detail();
            });
            $res['max'] = $grade->max_create_class;
        }else{
            // 学生部分
            $this->checkScheduleData($id); // 先校验数据

            $course = Schedule::where("student_id",$id)
                ->whereIn("status",[1,2])
                ->get()
                ->map(function($item){
                    return $item->course()->first()->detail();
                })
            ;
            $res['max'] =$grade->max_select_class;
        }
        $res['course'] = $course;
        return $this->json(1,$res);
    }

    public function postCreateCourse(){
        // 鉴权
        if(!$this->isTeacher()){
            return $this->toast(0,"非教师用户,无权限操作");
        }
        if(!$this->isCreateClass()){
            return $this->toast(0,"已达到最大课题数,不能再创建课题");
        }
        $user = $this->getUser();
        $isCreate = Course::create([
            "title"=>request()->title,
            "details"=>request()->details,
            "teacher_id"=> $user->id,
            "grade_id"=> $this->getGrade()->id,
            "institute_id" =>$user->institute_id,
            "major_id"=>$user->major_id,
            "status"=> 1,
        ]);

        return $this->toast($isCreate? 1:0, $isCreate?"创建成功":"创建失败");
    }

    public function isCreateClass(){
        // 判断是否可以创建课程
        $hasCreateNum = $this->getUser()->course()
            ->where("status","!=",0)->count();
        if($hasCreateNum < $this->getGrade()->max_create_class){
            return true;
        }
        return false;
    }

    private function checkScheduleData($id){

        $sc = Schedule::where("student_id",$id)->get(); // 获取所有相关的进度信息
        $sc->each(function($item){
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
        });
    }
    private function checkCourseData($id){
        // 课程信息必须一致保持一致,即使出错也只能恢复到待审核状态,不可直接删除
        $course_list = Course::where("teacher_id",$id)->get();
        $course_list->each(function($item){
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
                $item->schedule()->where("status",1)
                    ->update(["status"=>1]); // 已完成互选的课程,将所有"选定中"的人改为退选
            }
        });

    }
}
