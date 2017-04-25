<?php

namespace App\Http\Controllers\Admin;

use App\Model\Institute;
use App\Model\Major;
use App\Model\Teacher;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TeacherApi extends Controller
{
    private $instituteHandle,$institute_id,$grade_id;
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
        $this->instituteHandle->teacher()
            ->whereIn('id', request()->teacher_list)
            ->delete(); // 软删除老师
        // TODO： 删除老师并删除老师相关的课程
        return $this->json(1, '删除成功');
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
