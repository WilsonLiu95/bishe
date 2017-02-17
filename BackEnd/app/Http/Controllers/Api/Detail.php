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
        if($course->status == 1 ){
            // 在互选中
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
        if ($course->status !== 1) {
            $this->error['msg'] = $course->status == 0 ? "已完成互选,不可再选定":"课程已被删除";
            return response()->json($this->error);
        }
        // 可以被选定,再校验该用户是否已经选定
        $sc = Model\Schedule::firstOrNew([
            "course_id" => $id,
            "student_id" => $this->getSessionInfo("id"),
        ]);
        $this->success['msg'] = "选定成功,请主动联系老师,完成互选";
        $this->success['data'] = $sc;
        if ($sc->exists){
            if($sc->status == 2){
                // 2代表选定后退选
                $sc->status = 1;
                $sc->save(); // 更新字段为1
            }else if($sc->status == 1){
                $this->success['msg'] = "您已经选定了该门课程,无需再选";
            }else if ($sc->status == 0){
                $this->success['msg'] = "已完成互选,请勿再选定";  // 理论上,如果完成互选是走不到这里来的。先保留
            }

        } else {
            $sc->status = 1;
            $sc->save();
        }
        return response()->json($this->success);

    }

}
