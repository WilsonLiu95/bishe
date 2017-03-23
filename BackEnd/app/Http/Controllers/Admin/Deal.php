<?php

namespace App\Http\Controllers\Admin;

use App\Model\Student;
use App\Model\Teacher;
use App\Model\Course;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Deal extends Controller
{
    //
    public function getIndex(){
        $teacher['tea_register'] = Teacher::where('openid','!=','')->count().'/'.Teacher::count();
        $teacher['stu_register'] = Student::where('openid','!=','')->count().'/'.Student::count();
        $teacher['course_total'] = Course::whereIn('check_status',['0','1','2'])->count();
        $teacher['course_confirm'] = Course::where('check_status','2')->count();
        $teacher['course_review'] = Course::where('check_status','0')->count();
        return $this->json(1,$teacher);
    }

    public function getTeaTable(){
        $teacher = Teacher::with('course')->get()->toArray();
        for ($i=0; $i<Teacher::count();$i++) {
            $teacher[$i]['course_num']=count($teacher[$i]['course']);
//          array_except($teacher, 'course');
//          $tranlate=["计算机","通信"];
//          $tranlate[$major_id]
        }
        $teacher = ->paginate(15);
        return $this->json(1,$teacher);
    }

    public function getTeaDialog(){
        $id = request()->id;
        $teacher = Teacher::with('course')->find($id);
        $teacher['course_num'] = count($teacher['course']);
        return $this->json(1, $teacher);
    }

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

    public function postTeaEdit(){
        $id = request()->id;
        $is_admin = request()->is_admin;
        $teacher['isSuccess']= Teacher::find($id)->update(['is_admin' => $is_admin]);
        return $this->json(1, $teacher);
    }

    public function postTeaDelete(){
        $id = request()->id;
        $teacher['isSuccess'] = Teacher::where('id',$id)->delete();
        return $this->json(1, $teacher);
    }

    public function getTeaSearch(){
        $searcher = request()->searcher;
        $teacher = Teacher::where('name','like','%'. $searcher.'%')->orWhere('job_num',$searcher)->get();
        return $this->json(1,$teacher);
    }
}
