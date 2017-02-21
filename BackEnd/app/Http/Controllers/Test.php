<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ScheduleTab;
use App\Model\Course;
use App\Model\Schedule;
use App\Model\Student;
use App\Model\Teacher;
use Faker\Factory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Psy\Util\Json;

class Test extends Controller
{
    public function getIndex(Request $request)
    {
//        $this->checkScheduleData(1);
        Student::all()->each(function($st){
            $this->checkScheduleData($st->id);
        });
//        Teacher::all()->each(function($st){
//            $this->checkCourseData($st->id);
//        });
    }


    public function checkScheduleData($id){

        $sc = Schedule::where("student_id",$id)->get(); // 获取所有相关的进度信息
        $sc->each(function($item){
            // 首先看有无该课程
            $course = $item->course()->exists() ? $item->course()->first() : $item->delete(); // 如果课程不存在,则必然是错误数据
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
    public function checkCourseData($id){
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
    public function getSet(Request $request)
    {
        var_dump(request()->segment(1));
        $this->success['data'] =session()->all();
        return response()->json($this->success);
    }
    public  function postIndex(){
        session()->put("testindex","tttttt");
        $this->success['data'] =session()->all();
        $this->success['sid'] =session()->getid();
        return response()->json($this->success);
    }
    public  function getInfo(){

        return response()->json(Teacher::find(1)->account);
    }

}
