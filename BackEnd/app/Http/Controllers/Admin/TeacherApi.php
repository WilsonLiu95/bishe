<?php

namespace App\Http\Controllers\Admin;

use App\Model\Institute;
use App\Model\Major;
use App\Model\Message;
use App\Model\Teacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TeacherApi extends Controller
{
    private $instituteHandle, $institute_id, $grade_id;

    function __construct()
    {
        parent::__construct();

        $this->institute_id = session()->get('id');
        $this->instituteHandle = Institute::find($this->institute_id);
        $this->grade_id = $this->instituteHandle->grade->count() == 1 ? $this->instituteHandle->grade()->value('id') : null;
    }

    public function getTeacherInit()
    {
        $option = \GuzzleHttp\json_decode(request()->input('option'), true);
        $option['where'] = [
            ['institute_id', '=', 1], // 限制为自己学院
        ];
        $data['teacher_list'] = $this->makePage(Teacher::class, $option);

        // 制作映射关系
        $data['major_map'] = Major::where('institute_id', $this->institute_id)
            ->lists('name', 'id')->toArray();
        return response()->json($data);
    }

    public function postTeacherSubmit()
    {
        $this->validate(request(), [
            'id' => 'required|integer',
            'name' => 'required',
            'job_num' => 'required',
        ]);
        $new = request()->only('name', 'job_num', 'is_admin', 'major_id');
        if (request()->id) {
            // 修改classes_id但不修改classes_code 用于记录导入时是哪个班级
            Teacher::find(request()->id)->update($new);
        } else {
            // 新建的不写入classes_code 用以标识是手动创建
            $new['institute_id'] = $this->institute_id;
            Teacher::create($new);
        }

        return $this->toast(1, '修改成功');
    }


    public function postDelete()
    {
        $this->validate(request(), [
            'teacher_list' => 'required|array',
        ]);
        $teacher_list = $this->instituteHandle->teacher()
            ->whereIn('id', request()->teacher_list)
            ->get(); // 软删除老师
        $teacher_list->each(function ($teacher) {
            // 遍历老师列表，进行删除
            $this->deleteOneTeacher($teacher->id);
        });
        return $this->json(1, '删除成功');
    }

    private function deleteOneTeacher($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        if ($teacher && $this->grade_id) {
            $courseHasFinish = $teacher->course()->where('status', 3)->where('grade_id', $this->grade_id)->get();
            $courseSelect = $teacher->course()->where('status', 2)->where('grade_id', $this->grade_id)->get();
            
            $courseHasFinish->each(function ($course) use ($teacher) {
                // 对已完成互选的课程进行遍历
                $sc = $course->schedule()->first();
                if ($sc) {
                    Message::create([
                        'title' => "退选通知",
                        'send_type' => 2,
                        'send_id' => $sc->student_id,
                        'content' => "因为系统将" . $teacher->name . "老师删除，导致您的《" . $course->title . "》课程被自动退选。"
                    ]);
                    $course->schedule()->delete(); // 删除选课记录
                }
            });
            $courseSelect->each(function ($course) use ($teacher) {
                // 对互选中的课程进行遍历
                $course->schedule()->get()->each(function ($sc) use ($course, $teacher) {
                    // 对选择该课程的学生们进行遍历
                    Message::create([
                        'title' => "退选通知",
                        'send_type' => 2,
                        'send_id' => $sc->student_id,
                        'content' => "因为系统将" . $teacher->name . "老师删除，导致您的《" . $course->title . "》课程被自动退选。"
                    ]);
                });
                $course->schedule()->delete(); // 删除选课记录
            });
            $teacher->course()->delete(); // 将课程全部删除
            $teacher->delete();
        }
    }

    public function postFile()
    { // 上传excel
        if (request()->hasFile('excel')) {
            if (request()->file('excel')->isValid()) {
                $name = 'ins' . $this->institute_id . '_' . request()->file('excel')->getClientOriginalName();
                $path = storage_path('app') . '/bishe/teacher_excel/' . $name;
                request()->file('excel')->move(storage_path('app') . '/bishe/teacher_excel/', $name);
                return $this->importTeacher($path);
            } else {
                return $this->toast(0, '文件无效');
            }
        } else {
            return $this->toast(0, '请选择正确的文件类型');
        }
    }

    private function importTeacher($excelPath)
    { // 处理用于上传的excel
        $phpexcel = new \PHPExcel_Reader_Excel2007();
        if (!$phpexcel->canRead($excelPath)) {
            return $this->toast(0, '不可读,仅支持EXCEL2007及以上的版本');
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
        if ($sheetHeader[0] !== ['姓名', '工号', '专业']) {
            return $this->toast(0, '输入格式请规范,A1:姓名,A2:工号,A3:专业');
        }
        $insert = [];

        $major_map = Major::where('institute_id', $this->institute_id)
            ->lists('id', 'name')->toArray();
        $create_at = date("Y-m-d H:i:s");

        $teacher = [ // 通用模板
            'created_at' => $create_at,
            'updated_at' => $create_at,
            'institute_id' => $this->institute_id,
        ];
        foreach ($sheetbody as $key => $item) {
            if ($item[0] && $item[1] && $item[2]) {
                $teacher['name'] = $item[0];
                $teacher['job_num'] = $item[1];

                if ($major_map[$item[2]]) {
                    $teacher['major_id'] = $major_map[$item[2]];
                    if (Teacher::where('institute_id', $this->institute_id)->where('job_num', $item[1])->count()) {
                        return $this->toast(0, '请检查第' . ($key + 1) . '条数据,数据库中已存在该学号的老师,请校验');
                    } else {
                        $insert[] = $teacher; // 压入待插入的数组
                    }
                } else {
                    return $this->toast(0, '请检查第' . ($key + 1) . '条数据,专业信息出错');
                }
            } else {
                return $this->toast(0, '请检查第' . ($key + 1) . '条数据,数据不能为空');
            }
        }
        $result = Teacher::insert($insert);
        return $this->toast($result ? 1 : 0, $result ? '数据全部导入成功' : '数据插入失败');
    }
}
