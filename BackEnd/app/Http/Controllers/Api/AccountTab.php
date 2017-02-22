<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AccountTab extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

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
}
