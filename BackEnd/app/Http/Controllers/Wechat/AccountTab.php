<?php

namespace App\Http\Controllers\Wechat;

use App\Model;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AccountTab extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        $data = $this->getUser()->account();
        return $this->json(1,$data);
    }
    public function postModify()
    {
        if($this->isTeacher()){
            // 老师
            $update = Input::only("intro","qq","email","phone");
        } else if($this->getSessionInfo("type") == 2){
          // 学生
            $update = Input::only("intro","qq","email","phone");
        }

        $this->getUser()->update($update);
        return $this->toast(1,"账户信息修改成功");
    }
    public function getInfo(){
        if(Input::get("type") == "teacher"){
            $teacher_id = Model\Course::find(request()->id)
                ->teacher_id;
            $account = Model\Teacher::find($teacher_id)->account();
        }else{
            $student_id = Model\Course::find(request()->id)
                ->schedule()->where("status",1)->get()[request()->index]->student_id;
            $account = Model\Student::find($student_id)->account();

        }
        return $this->json(1,$account);

    }
    public function getIsTeacher(){
        return $this->json(1,$this->isTeacher());
    }


}
