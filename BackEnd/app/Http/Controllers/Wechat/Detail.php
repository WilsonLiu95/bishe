<?php

namespace App\Http\Controllers\Wechat;


use App\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Detail extends Controller
{

    public function getIndex()
    {
        $id =  request()->input("id");
        $this->checkOneCourse($id);// 校验课程数据

        $course = Model\Course::find($id);

        $course->detail(); // 补充详情

        // 查询是否是发布课题的老师
        if($this->isTeacher()){
            $course->isowner = $this->isOwner($id);
            $course->isadmin = $this->isCourseAdmin($id);
        } else if($course->status == 2 ){
            // 学生,课程在互选中
            $course->isSelected = $course->schedule()
                ->where("student_id",$this->getSessionInfo("id"))
                ->where("status",1)->exists();
        }
        return $this->json(1,$course);
    }

    public function getSelectCourse(){
        if($this->isMaxSelectCourse()){
            return $this->toast(0,"已达到最大课程数,不可再选定课程");
        }
        $id =  request()->input("id");
        $course = Model\Course::find($id);
        // 首先检验该课程是否可以被选定
        if ($course->status != 2) {
            return $this->toast(0,"课程未进入互选阶段");
        }
        // 可以被选定,再校验该用户是否已经选定
        $sc = Model\Schedule::firstOrNew([
            "course_id" => $id,
            "student_id" => $this->getSessionInfo("id"),
        ]);
        if ($sc->exists){
            if($sc->status == 0){
                // 2代表选定后退选
                $sc->update(['status'=>1]); // 更新字段为1
            }else if($sc->status == 1){
                return $this->toast(0,"您已经选定了该门课程,无需再选");
            }
//            else if ($sc->status == 2){
//                $this->success['msg'] = "已完成互选,请勿再选定";  // 理论上,如果完成互选是走不到这里来的。先保留
//            }

        } else {
            // 用户尚未选定过该课程
            $sc->status = 1;
            $sc->save(); // 新建schedule
        }
        return $this->toast(1,"选定成功,请主动联系老师,完成互选");

    }

    public function postModifyCourse()
    {
        if (!$this->isOwner(request()->id) || is_null(request()->id))
        {
            return $this->toast(0,'不可修改不属于您的课程');
        }
        $course = Model\Course::find(request()->id);

        $course->update([
            "title"=>request()->title,
            "details"=>request()->details
        ]);
        if($course->status==1 && $course->check_status ==1){ // 未通过审核的课程修改后,即重新回到待审核
            $course->update(["check_status"=>0]);
        }
        return $this->toast(1,"修改成功");
    }
    public function getDeleteCourse(){
        $course = Model\Course::find(request()->id);

        if ($course->status == 3){
            // 已完成互选,不能直接删除,需要先退选学生恢复到"互选中",才可以继续删除
            return $this->toast(0,"已完成互选,不能直接删除");
        }

        if($course->status == 2){ // 互选中的课程
            // 接着退选选中该门课的学生
            $schedule_list = $course->schedule()
                ->where("status",1)->get();
            $schedule_list->each(function ($item) {
                // 将所有学生退选
                // message 向所有学生发送消息
                $item->update(['status'=>0]);
            });
        }
        // 最后删除课程,不可先删除课程否则 status始终为0 上面2个if将无效
        $course->update(['status'=>0]);// 0代表删除该课程
        return $this->toast(1,"课程删除成功");
    }
    public function getStudentList(){
        $course = Model\Course::find(request()->id)
            ->schedule()->where("status",1)->get();
        return $this->json(1,$course);
    }


    public function getSelectStudent(){
    // 选中学生
        $course = Model\Course::find(request()->id);
        $course->update(['status'=>3]);

        $course->schedule()
            ->where("student_id","!=",request()->student_id)
            ->update(["status"=>0]); // 首先将其他所有人置为0,再将选中的个体置为2
        $course->schedule()
            ->where("student_id",request()->student_id)
            ->update(["status"=>2]);

        return $this->toast(1,"已完成互选，马上为您自动跳转");
    }
    public function getDeleteStudent(){
        $course = Model\Course::find(request()->id);
        $course->update(["status"=>2]);
        $course->schedule()
            ->where("status",2)
            ->update(['status'=>0]); // 老师方面退选学生,学生回到退选状态。
        return $this->toast(1,"退选学生成功");
    }
    public function getCancelSelectCourse(){
        $student_id = $this->getSessionInfo("id");
        Model\Course::find(request()->id)
        ->schedule()
            ->where("student_id",$student_id)
        ->first()->update(['status'=>0])
        ;
        return $this->toast(1,"退选课程成功");
    }
    public function postCheckCourse(){
        $course = Model\Course::find(request()->id);
        if(request()->is_pass){
            // 通过审核
            $course->update([
                    'check_status'=>2,
                    'check_advice'=>request()->check_advice,
                    'status'=>2
                ]
            );
        }else{
            // 通过审核
            $course->update([
                    'check_status'=>1,
                    'check_advice'=>request()->check_advice,
                    'status'=>1
                ]
            );
        }
        return $this->toast(1,"审核完成");



    }
    private function isMaxSelectCourse(){
        $num = $this->getUser()->schedule()->whereIn('status',[1,2])->count();
        $max = $this->getGrade()->max_select_class;
        return $num < $max ? false: true;
    }
    // 判断是否为该课程的主人,以鉴定权限
    private function isOwner($course_id){
        $course = Model\Course::find($course_id);
        return $course->teacher_id == $this->getSessionInfo("id");
    }
    private function isCourseAdmin($course_id){
        $course = Model\Course::find($course_id);
        if(!$this->isTeacher() || !$this->getUser()->isMajorAdmin() ||
            $course->major_id != $this->getUser()->major_id
        ){ // 不是老师 or 不是管理员 or 专业不同
            return false;
        }
        return true;
    }
}
