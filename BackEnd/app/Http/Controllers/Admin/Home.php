<?php

namespace App\Http\Controllers\Admin;

use App\Model\Grade;
use App\Model\Institute;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\Major;
use App\Model\Student;
use App\Model\Teacher;
use App\Model\Course;
use Illuminate\Support\Facades\DB;

class Home extends Controller
{
    //  注册统计
    private $instituteHandle, $institute_id;
    private $grade_id;

    function __construct()
    {
        parent::__construct();
        $this->institute_id = session()->get('id');
        $this->instituteHandle = Institute::find($this->institute_id);
        $this->grade_id = $this->instituteHandle->grade->count() == 1 ? $this->instituteHandle->grade()->value('id') : 0;
    }

    public function getGrade()
    {
        $gradeList = [
            [
                'label' => '当前年份',
                'options' => []
            ],
            [
                'label' => '历史年份',
                'options' => []
            ]
        ];
        $grade = DB::table('grade')
            ->where('institute_id', $this->institute_id)
            ->get();
        foreach ($grade as $item){
            if($item->deleted_at){
                $gradeList[1]['options'][] = [
                    'label'=> $item->name,
                    'value'=> $item->id
                ];
            }else{
                $data['config'] = [
                    'is_open'=>$item->is_open,
                    'max_select_class'=>$item->max_select_class,
                    'max_create_class'=>$item->max_create_class
                ];
                $gradeList[0]['options'][] = [
                    'label'=> $item->name,
                    'value'=> $item->id
                ];
            }

        }
        $data['gradeList'] = $gradeList;

        return $this->json(1, $data);
    }

    public function getIndex()
    {
        $grade_id = request()->only('grade_id');
        $isExists = DB::table('grade')
            ->where('institute_id', $this->institute_id)
            ->where('id', $grade_id)
            ->exists();
        if (!$isExists) {
            return $this->json(0);
        }
        $data['tea_register'] = $this->instituteHandle->teacher()->where('openid', '!=', '')->count() . '/' .
            $this->instituteHandle->teacher()->count();
        $data['stu_register'] = $this->instituteHandle->student()
                ->where('grade_id', $grade_id)
                ->where('openid', '!=', '')->count() . '/' .
            $this->instituteHandle->student()
                ->where('grade_id', $grade_id)
                ->count();
        $data['course_total'] = $this->instituteHandle->course()
            ->where('grade_id', $grade_id)
            ->whereIn('status', ['1', '2', '3'])->count();
        $data['course_confirm'] = $this->instituteHandle->course()
            ->where('grade_id', $grade_id)
            ->where('status', '3')->count();
        $data['course_review'] = $this->instituteHandle->course()
            ->where('grade_id', $grade_id)
            ->where('status', '1')
            ->count();
        return $this->json(1, $data);
    }
    public function postModify(){
        $config = request()->only('max_select_class','max_create_class');
        $isSuccess = Grade::find($this->grade_id)->update($config);
        return $this->toast($isSuccess, $isSuccess?'修改成功':'修改失败');
    }
    public function postFinishGrade(){
        $isSuccess = Grade::find($this->grade_id)->delete();
        return $this->toast($isSuccess, $isSuccess?'修改成功':'修改失败');
    }
    public function postCreateGrade(){
        $this->validate(\request(),[
            'gradeName'=>'required'
        ]);
        $canCreateGrade = !Grade::where('institute_id', $this->institute_id)->count();
        if ($canCreateGrade){
            $isSuccess = Grade::create([
                'institute_id'=>$this->institute_id,
                'max_select_class'=>2,
                'max_create_class'=>5,
                'name'=>request()->gradeName
            ]);
            return $this->toast($isSuccess, $isSuccess?'创建成功':'创建失败');
        }else{
            return $this->toast(0, '当前仍有未结束的学年');
        }

    }

}
