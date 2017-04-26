<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin;
use App\Model\Course;
use App\Model\Grade;
use App\Model\Schedule;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class manage extends Controller
{
    //获取课程表
    public function getIndex()
    {
        $select = Grade::lists('name', 'id');
        $current_year = Grade::where('status', '1')->value('id');
        $year = request()->year ? request()->year : $current_year;
        $course = Course::where('grade_id', $year)->paginate(10)->toArray();  //teacher_id、major_id、status需要转译！
        $data['select'] = $select;
        $data['course'] = $course;
        $data['current_year'] = $current_year;
        return $this->json(1, $data);
    }

    //获取学生表
    public function postStuTable()
    {
        $current_year = request()->current_year;
        $select_year = request()->select_year ? request()->select_year : $current_year;
        $student = Student::where('grade_id', $select_year)->paginate(10)->toArray();
        for ($i = 0; $i < count($student['data']); $i++) {   //determine需要转译！！
            $student['data'][$i]['determine'] = Schedule::where('student_id', $student['data'][$i]['id'])
                ->value('course_id');
        }
        $data['student'] = $student;
        return $this->json(1, $data);
    }

    //学生信息增加
    public function postStuAdd()
    {
        $name = request()->name;
        $year = request()->year;
        $job_num = request()->job_num;
        $student['isSuccess'] = Student::insert([
            'name' => $name,
            'job_num' => $job_num,
            'grade_id' => $year
        ]);
        return $this->json(1, $student);
    }

    //学生信息修改
    public function postStuEdit()
    {
        $id = request()->id;
        $name = request()->name;
        $job_num = request()->job_num;
        $student['isSuccess'] = Student::find($id)
            ->update([
                'name' => $name,
                'job_num' => $job_num
            ]);
        return $this->json(1, $student);
    }

    public function getStuDelete()
    {
        $id = request()->id;
        $student['isSuccess'] = Student::where('id', $id)->delete();
        return $this->json(1, $student);
    }

    //课程搜索
    public function getCourSearch()
    {
        $searcher = request()->searcher;
        $course = Course::where('title', 'like', '%' . $searcher . '%')
            ->paginate(10)
            ->toArray();
        return $this->json(1, $course);
    }

    //学生搜索
    public function getStuSearch()
    {
        $searcher = request()->searcher;
        $student = Student::where('name', 'like', '%' . $searcher . '%')
            ->orWhere('job_num', $searcher)
            ->paginate(10)
            ->toArray();
        return $this->json(1, $student);
    }

    //年份增加
    public function postYearAdd()
    {
        $new_year = request()->new_year;
        $max_create_class = request()->max_create_class;
        $max_select_class = request()->max_select_class;
        $year['isSuccess'] = Grade::insert([
            'name' => $new_year,
            'status' => 1,
            'max_create_class' => $max_create_class,
            'max_select_class' => $max_select_class
        ]);
        return $this->json(1, $year);
    }

    //选择系统状态
    public function postSystemStatus()
    {
        $year = request()->year;
        $status = request()->status;
        $system_status['isSuccess'] = Grade::where('id', $year)
            ->update([
                'status' => $status,
            ]);
    }
}
