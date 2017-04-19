<?php

namespace App\Http\Controllers\Admin;

use App\Model\Major;
use App\Model\Student;
use App\Model\Teacher;
use App\Model\Course;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Deal extends Controller
{
//  注册统计
    public function getIndex(){

        $teacher['tea_register'] = Teacher::where('openid','!=','')->count().'/'.Teacher::count();
        $teacher['stu_register'] = Student::where('openid','!=','')->count().'/'.Student::count();
        $teacher['course_total'] = Course::whereIn('check_status',['0','1','2'])->count();
        $teacher['course_confirm'] = Course::where('check_status','2')->count();
        $teacher['course_review'] = Course::where('check_status','0')->count();
        return $this->json(1,$teacher);
    }
//  获取导师表
    public function getTeacher(){
        $teacher = Teacher::with('course')   //底部分页栏
            ->paginate(10)->toArray();

        for ($i=0; $i<count($teacher['data']);$i++) {
            $teacher['data'][$i]['course_num']=count($teacher['data'][$i]['course']);
        }
        $data['teacher'] = $teacher;
        $major = Major::lists('name','id');
        $data['tran'] = $major;
        return $this->json(1,$data);
    }

    public function postMajorFilter() {
        $value = request()->value;
        $teacher = Teacher::where('major_id',$value)->paginate(10)->toArray();
        $data['teacher'] = $teacher;
        return $this->json(1,$data);
    }

//  打开对话框
    public function getTeaDialog(){
        $id = request()->id;
        $teacher = Teacher::with('course')
            ->find($id);
        $teacher['course_num'] = count($teacher['course']);
        return $this->json(1, $teacher);
    }
//  导师新增
    public function postTeaAdd(){
        $name = request()->name;
        $job_num = request()->job_num;
        $major_id = request()->major_id;
        $is_admin = request()->is_admin;
        $teacher['isSuccess'] = Teacher::insert([
            'name' => $name,
            'job_num' => $job_num,
            'major_id' => $major_id,
            'is_admin' => $is_admin
        ]);
        return $this->json(1, $teacher);
    }
//  导师编辑
    public function postTeaEdit(){
        $id = request()->id;
        $is_admin = request()->is_admin;
        $teacher['isSuccess']= Teacher::find($id)
            ->update(['is_admin' => $is_admin]);
        return $this->json(1, $teacher);
    }
//  导师删除
    public function postTeaDelete(){
        $id = request()->id;
        $teacher['isSuccess'] = Teacher::where('id',$id)
            ->delete();
        return $this->json(1, $teacher);
    }
//  导师搜索
    public function getTeaSearch(){
        $searcher = request()->searcher;
        $teacher = Teacher::where('name','like','%'. $searcher.'%')
            ->orWhere('job_num',$searcher)
            ->paginate(10)
            ->toArray();
        return $this->json(1,$teacher);
    }
}
