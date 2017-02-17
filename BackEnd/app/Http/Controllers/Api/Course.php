<?php

namespace App\Http\Controllers\Api;

use App\Model\Schedule;
use App\Model\Student;
use App\Model\Teacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Psy\Util\Json;

class Course extends Controller
{
    public function getIndex()
    {
        $page = Input::get('page', 1);
        $search = Input::get('search', "");
        $paginate = 8;
        $dataTwo = \App\Model\Course::where("status",0)

            ->join("teacher",'course.teacher_id','=',"teacher.id") // 时间换空间
            ->select("course.*","teacher.name as teacher_name")
            ->where(function($query) use ($search) {
                $query->where("teacher.name","like","%". $search ."%")
                    ->orWhere("course.title","like","%". $search ."%");
            })
            ->orderBy("updated_at","desc")
        ;
        $data = \App\Model\Course::where("status",1)

            ->join("teacher",'course.teacher_id','=',"teacher.id") // 时间换空间
            ->select("course.*","teacher.name as teacher_name")
            ->where(function($query) use ($search) {
                $query->where("teacher.name","like","%". $search ."%")
                    ->orWhere("course.title","like","%". $search ."%");
            })
            ->orderBy("updated_at","desc")
            ->union($dataTwo)
            ->get()->toArray()
        ;
        $slice = array();
        if (!empty($data)){
            // 如果为空则返回一个空数组,不进行array_slice否则会报错
//            return response()->json($data);
            $slice = array_slice($data, $paginate * ($page - 1), $paginate);
        }
        $result = (new LengthAwarePaginator($slice, count($data), $paginate, $page))->toArray();
        $res = array();
        foreach ($result['data'] as $item) {
            $res[] = $this->fullCourse($item);
        }
        $result['data'] = $res;
        return response()->json($result);
    }
    private function fullCourse($orign){
        // 进度相关数据
        $schedule = Schedule::where('course_id',$orign['id']);
        $orign['num'] = $schedule->count();


        return $orign;
        
    }

}
