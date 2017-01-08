<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Course extends Controller
{
    public function page($option)
    {

        $table = DB::table("course");
        return $table->where($option->where)->paginate(10);
//        return $table->where($option->where)->orderBy($option->order->name,$option->order->way)->paginate($option->pageSize);
    }
    public function postIndex()
    {
        $option  = request()->input();
        $json = $this->page($option);
        return response()->json($json);
    }

}
