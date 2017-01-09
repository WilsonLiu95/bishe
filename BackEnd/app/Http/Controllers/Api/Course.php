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
        $dataTwo = \App\Model\Course::where(["course.status"=>2])
            
            ->orderBy("updated_at","desc")
        ;
        $data = \App\Model\Course::where(["course.status"=>1])
            ->orderBy("updated_at","desc")
            ->union($dataTwo)
            ->get()->toArray()
        ;
        $slice = array_slice($data, $paginate * ($page - 1), $paginate);

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
        foreach($schedule->get() as $s){
            $orign['student_name'][] = Student::find($s['student_id'])->name;
            $orign["student_id"][]=  $s['student_id'];
        }
        // 老师相关数据
        $teacher = Teacher::find($orign['teacher_id']);
        $orign['teacher_name'] = $teacher['name'] ? $teacher['name']: ""  ;

        return $orign;
        
    }

}
