<?php

namespace App\Http\Controllers\Api;

use App\Model\Student;
use App\Model\Teacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class Account extends Controller
{
    private $userClass;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if(session()->get("type")){
            $this->userClass = session()->get("type") == "student" ? Student::class : Teacher::class;
        }

    }

    public function getIndex()
    {
        $id = session()->get("id");

        $a = $this->userClass::find($id);
        return response()->json($a);
    }
    public function postModify()
    {

        $update = Input::only("intro","qq","email","phone");
        $this->user->update($update);
        return $this->success;
    }
}
