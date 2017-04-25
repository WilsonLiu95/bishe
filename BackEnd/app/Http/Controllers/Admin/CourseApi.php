<?php

namespace App\Http\Controllers\Admin;

use App\Model\Course;
use App\Model\Institute;
use App\Model\Major;
use App\Model\Schedule;
use App\Model\Teacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CourseApi extends Controller
{
    private $instituteHandle,$institute_id;
    private $grade_id;
    function __construct()
    {
        parent::__construct();
        $this->institute_id = session()->get('id');
        $this->instituteHandle = Institute::find($this->institute_id);
        $this->grade_id = $this->instituteHandle->grade->count() == 1 ? $this->instituteHandle->grade()->value('id') : null;
    }
    public function getCourseInit(){
        $option = \GuzzleHttp\json_decode(request()->input('option'),true);

        $option['where'] = [
            ['institute_id','=',1], // 限制为自己学院
        ];
        $initCourse = $this->makePage(Course::class, $option);
        $initCourse['data'] = array_map(function ($item){
            $teacher = Teacher::find($item['teacher_id']);
            $item['teacher_name'] = $teacher ? $teacher->name : '已不存在该老师';
            $item['student_list'] = Schedule::where('course_id', $item['id'])
                ->whereIn('status',[1,2])->get()->pluck('student_name');
            return $item;
        }, $initCourse['data']);
        // 制作映射关系
        $data['major_map'] = Major::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        $data['course_list'] = $initCourse;
        return response()->json($data);
    }

}
