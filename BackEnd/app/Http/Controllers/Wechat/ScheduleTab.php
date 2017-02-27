<?php

namespace App\Http\Controllers\Wechat;


use App\Model\Course;
use App\Model\Message;
use App\Model\Schedule;
use App\Model\Teacher;

use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ScheduleTab extends Controller
{
    use BaseTrait;
    public function getIndex(){
        $res = array();
        $id = $this->getSessionInfo("id");

        $grade = $this->getGrade();
        if($this->isTeacher()){
            $this->checkOneTeacher($id);

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
            $this->checkOneStudent($id); // 先校验数据

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
        // 简单的数据校验
        if(!request()->title || ! request()->details){
            return $this->toast(0,"请填写课题名称与详情");
        }
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
            "major_id"=> $user->major_id,
            "status"=> 1,
        ]);
        // 课程修改后,发送信息给管理员
        Teacher::where("major_id",$user->major_id)
            ->where('is_admin',1)
            ->get()->each(function($item) use ($user){
                Message::create([
                    'title' => "新课题待审核通知",
                    'send_type' => 2,
                    'send_id' => $item->id,
                    'from_id' => $user->id,
                    'from_type' => 2,
                    'content' => $this->getUser()->name ."老师的新创建了《". request()->title ."》课题,请您审核。"
                ]);
            });

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


}
