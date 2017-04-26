<?php

namespace App\Http\Controllers;

use App\Model\Course;

use App\Model\Teacher;
use Faker\Factory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

class Test extends Controller
{
    public function getIndex(Request $request)
    {

        return "laravel启动成功";
    }

    public function postTeacherSubmit()
    {
        $this->validate(request(), [
            'id' => 'required|integer',
            'name' => 'required',
            'job_num' => 'required',
        ]);
        $new = request()->only('name', 'job_num', 'classes_id');

        return $this->json(['msg' => '修改成功']);
    }

    public function postDelete()
    {
        $this->validate(request(), [
            'teacher_list' => 'required|array',
        ]);
        SelectCourse::whereIn('teacher_id', request()->teacher_list)->forceDelete(); // 删除学生的同时,删除学生选课的数据
        Teacher::where('institute_id', $this->institute_id)
            ->whereIn('id', request()->teacher_list)
            ->forceDelete();

        return $this->json(['msg' => '删除成功']);
    }

    public function postFile()
    { // 上传excel
        if (request()->hasFile('excel')) {
            if (request()->file('excel')->isValid()) {
                $name = 'ins' . $this->institute_id . '_' . request()->file('excel')->getClientOriginalName();
                $path = storage_path('app') . '/select-course/student_excel/' . $name;
                request()->file('excel')->move(storage_path('app') . '/select-course/student_excel/', $name);
                return $this->importTeacher($path);
            } else {
                return $this->errorMsg('文件无效');
            }
        } else {
            return $this->errorMsg('请选择正确的文件类型');
        }
    }

    private function importTeacher($excelPath)
    { // 处理用于上传的excel
        $phpexcel = new \PHPExcel_Reader_Excel2007();
        if (!$phpexcel->canRead($excelPath)) {
            return ['msg' => '不可读,仅支持EXCEL2007及以上的版本', 'status' => false];
        }
        $sheet = $phpexcel->load($excelPath)->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $sheetHeader = $sheet->rangeToArray(
            'A1:' . 'C' . 1,
            NULL, TRUE, FALSE
        );
        $sheetbody = $sheet->rangeToArray(
            'A2:' . 'C' . $highestRow,
            NULL, TRUE, FALSE
        );
        if ($sheetHeader[0] !== ['姓名', '学号', '班级代号']) {
            return ['msg' => '输入格式请规范,A1:姓名,A2:学号,A3:班级代号', 'status' => false];
        }
        $insert = [];
        $classes_map = Classes::where('institute_id', $this->institute_id)
            ->lists('id', 'classes_code')->toArray();
        $major_map = Classes::where('institute_id', $this->institute_id)
            ->lists('major_id', 'classes_code')->toArray();
        $major_map[0] = 0;
        $classes_map[0] = 0;
        $create_at = date("Y-m-d H:i:s");

        $student = [ // 通用模板
            'created_at' => $create_at,
            'updated_at' => $create_at,
            'institute_id' => $this->institute_id,
            'grade_id' => Grade::where('institute_id', $this->institute_id)->value('id'),
        ];
        foreach ($sheetbody as $key => $item) {
            if ($item[0] && $item[1] && $item[2]) {
                $student['name'] = $item[0];
                $student['job_num'] = $item[1];
                $student['classes_code'] = $item[2];
                if ($major_map[$item[2]] && $classes_map[$item[2]]) {
                    $student['major_id'] = $major_map[$item[2]];
                    $student['classes_id'] = $classes_map[$item[2]];
                    if (Teacher::where('institute_id', $this->institute_id)->where('job_num', $item[1])->count()) {
                        return ['status' => false, 'msg' => '请检查第' . ($key + 1) . '条数据,数据库中已存在该学号的学生,请校验'];
                    } else {
                        $insert[] = $student; // 压入待插入的数组
                    }

                } else {
                    return ['status' => false, 'msg' => '请检查第' . ($key + 1) . '条数据,班级代号出错'];
                }
            } else {
                return ['status' => false, 'msg' => '请检查第' . ($key + 1) . '条数据,数据不能为空,且班级代号不能为0'];
            }
        }
        $result = Teacher::insert($insert);
        return ['status' => $result, 'msg' => ($result ? '数据全部导入成功' : '数据插入失败')];
    }

}
