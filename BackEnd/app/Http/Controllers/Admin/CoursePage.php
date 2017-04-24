<?php

namespace App\Http\Controllers\Admin;

use App\Model\Course;
use App\Model\Institute;
use App\Model\Major;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CoursePage extends Controller
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
        $data['course_list'] = $this->makePage(Course::class, $option);

        Course::where('course.institute_id',1)
            ->where('course.grade_id', $option['filter']['grade_id'][0])
            ->join("teacher",'course.teacher_id','=',"teacher.id")
            ->select("course.*","teacher.name as teacher_name")
//            ->where(function($query) use ($search) {
//                $query->where("teacher.name","like","%". $search ."%")
//                    ->orWhere("course.title","like","%". $search ."%");
//            })
            ->get();
        return response()->json($data);
    }

}
