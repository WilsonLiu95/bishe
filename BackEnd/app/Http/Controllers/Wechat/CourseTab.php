<?php

namespace App\Http\Controllers\Wechat;

use App\Model\Course;
use App\Model\Schedule;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Input;


class CourseTab extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        $page = Input::get('page', 1);
        $search = Input::get('search', "");
        $paginate = 10;
        if($this->isTeacher()){
            $data = $this->getTeacherCourse($search);
        }else{
            $data = $this->getStudentCourse($search);
        }
        $slice = array();
        if (!empty($data)){
            // 如果为空则返回一个空数组,不进行array_slice否则会报错
            $slice = array_slice($data, $paginate * ($page - 1), $paginate);
        }
        $result = (new LengthAwarePaginator($slice, count($data), $paginate, $page))->toArray();
        $res = [];
        foreach($result['data'] as $index => $item){
             $res[] = $this->fullCourse($item);
        }
        $result['data'] = $res;

        return $this->json(1,$result);
    }
    private function getStudentCourse($search){
        $student = $this->getUser();
        $data = Course::whereIn('status',[2,3])
            ->where("grade_id",$student->grade_id) // 年级课程信息过滤
            ->where('course.institute_id',$student->institute_id)
            ->join("teacher",'course.teacher_id','=',"teacher.id")
            ->select("course.*","teacher.name as teacher_name")
            ->where(function($query) use ($search) {
                $query->where("teacher.name","like","%". $search ."%")
                    ->orWhere("course.title","like","%". $search ."%");
            })
            ->orderBy("status",'ASC')
            ->orderBy("updated_at",'DESC')
            ->get()->toArray()
            ;
        return $data;
    }
    private function getTeacherCourse($search){
        $teacher = $this->getUser();
        $data = Course::where(function($query) use($teacher){
                if($teacher->isMajorAdmin()){ // 这个老师是管理员
                    $query->whereIn('status',[1,2,3]); // 管理员可以看到没有审核的课程
                }else{
                    $query->whereIn('status',[2,3]);
                }
                })
            ->where("grade_id",$this->getGrade()->id) // 年级课程信息过滤
            ->where('course.institute_id',$teacher->institute_id) // 学院过滤
            ->where('course.major_id',$teacher->major_id) // 专业过滤
            ->join("teacher",'course.teacher_id','=',"teacher.id")
            ->select("course.*","teacher.name as teacher_name")
            ->where(function($query) use ($search) {
                $query->where("teacher.name","like","%". $search ."%")
                    ->orWhere("course.title","like","%". $search ."%");
            })
            ->orderBy("status",'ASC')
            ->orderBy("check_status",'ASC')
            ->orderBy("updated_at",'DESC')
            ->get()->toArray()
        ;
        return $data;
    }
    private function fullCourse($course){
        $sc = Schedule::where('course_id',$course["id"]);
        if($course['status']==2){
            // 互选中
            $schedule = $sc->where("status",1)->get();
            $course['student_num'] = $schedule->count();
        }
        if($course['status']==3){
            // 完成互选
            $schedule = $sc->where("status",2)->get();
            if($schedule->count()==1 && $schedule->first()->student->exists()){
                // 课程为互选完成,则必然有唯一的 互选学生
                $course['student_name'] = $schedule->first()->student->name;
            }else{
                $this->checkOneCourse($course['id']); // 数据错误,则进行校验更正
            }

        }
        return $course;
        
    }

}
