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
        $id = $this->getSessionInfo("id");
        $data = $this->getUser()->account($id);
        return response()->json($data);
    }
    public function postModify()
    {
        if($this->getSessionInfo("type") == 1 ){
            // 老师
            $update = Input::only("intro","qq","email","phone");
        } else if($this->getSessionInfo("type") == 2){
          // 学生
            $update = Input::only("intro","qq","email","phone");
        }

        $this->getUser()->update($update);
        return $this->success;
    }
}
