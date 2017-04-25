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
        $this->grade_id = $this->instituteHandle->grade->count() == 1 ? $this->instituteHandle->grade()->value('id') : null;
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
                $gradeList[0]['options'][] = [
                    'label'=> $item->name,
                    'value'=> $item->id
                ];
            }

        }

        return $this->json(1, $gradeList);
    }

    public function getIndex()
    {
        if ($this->grade_id === null) {
            return $this->json(0);
        }

        $teacher['tea_register'] = $this->instituteHandle->teacher()->where('openid', '!=', '')->count() . '/' .
            $this->instituteHandle->teacher()->count();
        $teacher['stu_register'] = $this->instituteHandle->student()
                ->where('grade_id', $this->grade_id)
                ->where('openid', '!=', '')->count() . '/' .
            $this->instituteHandle->student()
                ->where('grade_id', $this->grade_id)
                ->count();
        $teacher['course_total'] = $this->instituteHandle->course()
            ->where('grade_id', $this->grade_id)
            ->whereIn('status', ['1', '2', '3'])->count();
        $teacher['course_confirm'] = $this->instituteHandle->course()
            ->where('grade_id', $this->grade_id)
            ->where('status', '3')->count();
        $teacher['course_review'] = $this->instituteHandle->course()
            ->where('grade_id', $this->grade_id)
            ->where('status', '1')
            ->count();
        return $this->json(1, $teacher);
    }


}
