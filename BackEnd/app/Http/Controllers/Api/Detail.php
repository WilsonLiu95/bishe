<?php

namespace App\Http\Controllers\Api;


use App\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Detail extends Controller
{

    public function getIndex()
    {
        $id =  request()->input("id");
        $course = Model\Course::find($id);

        $course->detail(); // 补充详情

        // 查询是否是发布课题的老师
        if($this->isTeacher()){
            $course->isowner = $this->isOwner($id);
        } else if($course->status == 2 ){
            // 学生,课程在互选中
            $course->isSelected = $course->schedule()
                ->where("student_id",$this->getSessionInfo("id"))
                ->where("status",1)->exists();
        }
        return response()->json($course);
    }

    public function getSelect(){
        $id =  request()->input("id");
        $course = Model\Course::find($id);
        // 首先检验该课程是否可以被选定
        if ($course->status != 2) {
            $this->error['msg'] = "课程未进入互选阶段";
            return response()->json($this->error);
        }

        $this->success['msg'] = "选定成功,请主动联系老师,完成互选";
        // 可以被选定,再校验该用户是否已经选定
        $sc = Model\Schedule::firstOrNew([
            "course_id" => $id,
            "student_id" => $this->getSessionInfo("id"),
        ]);
        if ($sc->exists){
            if($sc->status == 0){
                // 2代表选定后退选
                $sc->status = 1;
                $sc->save(); // 更新字段为1
            }else if($sc->status == 1){
                $this->success['msg'] = "您已经选定了该门课程,无需再选";
            }
//            else if ($sc->status == 2){
//                $this->success['msg'] = "已完成互选,请勿再选定";  // 理论上,如果完成互选是走不到这里来的。先保留
//            }

        } else {
            // 用户尚未选定过该课程
            $sc->status = 1;
            $sc->save();
        }
        return response()->json($this->success);

    }

    public function postModify()
    {
        if (!$this->isOwner(request()->id) || is_null(request()->id))
        {
            $this->error['msg']='不可修改不属于您的课程';
            return response()->json($this->error);
        }

        $course = Model\Course::find(request()->id);

        $course->title = request()->title;
        $course->details = request()->details;

        $course->save();
        return response()->json($this->success);
    }
    public function getDelete(){
        $course = Model\Course::find(request()->id);

        if ($course->status == 3){
            // 已完成互选,不能直接删除,需要先退选学生恢复到"互选中",才可以继续删除
            return response()->json($this->error);
        }

        // 0代表删除该课程
        $course->status = 0;
        $course->save();

        // 接着退选选中该门课的学生
        $schedule_list = $course->schedule()->where("status",1)->get();
        $schedule_list->each(function ($item) {
            // 将所有学生退选
           $item->status = 0;
            // message 向所有学生发送消息
           $item->save();
        });
        return response()->json($this->success);
    }
    public function getStudentList(){
        $course = Model\Course::find(request()->id)
            ->schedule()->where("status",1)->get();
        return response()->json($course);
    }

    public function getStudentInfo(){
        $student_id = Model\Course::find(request()->id)
            ->schedule()->where("status",1)->get()[request()->index]->student_id;
        $student = Model\Student::find($student_id)->account();
        return response()->json($student);

    }

    public function getTeacherInfo(){
        $teacher_id = Model\Course::find(request()->id)
            ->teacher_id;
        $teacher = Model\Teacher::find($teacher_id)->account();
        return response()->json($teacher);

    }
    public function getSelectStudent(){
    // 选中学生
        $course = Model\Course::find(request()->id);
        $course->status = 3;
        $course->save();

        $course->schedule()
            ->where("status",1)
            ->update(["status"=>0]);
        $course->schedule()
            ->where("student_id",request()->student_id)
            ->update(["status"=>2])
        ;
    }
    public function getDeleteStudent(){
        $course = Model\Course::find(request()->id);
        $course->update(["status"=>2]);
        $course->schedule()
            ->where("status",2)
            ->update(['status'=>0]); // 老师方面退选学生,学生回到退选状态。

    }
    public function getCancelSelect(){
        $student_id = $this->getSessionInfo("id");
        Model\Course::find(request()->id)
        ->schedule()
            ->where("student_id",$student_id)
        ->first()->update(['status'=>0])
        ;
    }
    // 判断是否为该课程的主人,以鉴定权限
    private function isOwner($course_id){
        $course = Model\Course::find($course_id);
        return $course->teacher_id == $this->getSessionInfo("id");
    }
}
