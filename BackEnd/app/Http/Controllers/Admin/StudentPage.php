<?php

namespace App\Http\Controllers\Admin;

use App\Model\Institute;
use App\Model\Major;
use App\Model\Student;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StudentPage extends Controller
{
    private $instituteHandle,$institute_id,$grade_id;
    function __construct()
    {
        parent::__construct();
        $this->institute_id = session()->get('id');
        $this->instituteHandle = Institute::find($this->institute_id);
        $this->grade_id = $this->instituteHandle->grade->count() == 1 ? $this->instituteHandle->grade()->value('id') : null;
    }
    public function getStudentInit(){
        $option = \GuzzleHttp\json_decode(request()->input('option'),true);
        $option['where'] = [
            ['institute_id','=',1], // 限制为自己学院
        ];

        $data['student_list'] = $this->makePage(Student::class, $option);

        return response()->json($data);
    }

    public function postStudentSubmit(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'name'=>'required',
            'job_num'=>'required',
        ]);
        $new = request()->only('name',  'job_num');
        if(request()->id){
            // 修改classes_id但不修改classes_code 用于记录导入时是哪个班级
            Student::find(request()->id)->update($new);
        }else{
            // 新建的不写入classes_code 用以标识是手动创建
            $new['institute_id'] = $this->institute_id;
            $new['grade_id'] = $this->grade_id;
            Student::create($new);
        }
        return $this->toast(1, '修改成功');
    }
    public function postDelete(){
        $this->validate(request(),[
            'student_list'=>'required|array',
        ]);
        $this->instituteHandle->student()
            ->whereIn('id', request()->student_list)
            ->delete(); // 软删除老师
        // TODO： 删除老师并删除老师相关的课程
        return $this->toast(1, '删除成功');
    }
    public function postFile(){ // 上传excel
        if(request()->hasFile('excel')){
            if(request()->file('excel')->isValid()){
                $name = 'ins' . $this->institute_id . '_' . request()->file('excel')->getClientOriginalName();
                $path = storage_path('app') . '/bishe/student_excel/'. $name;
                request()->file('excel')->move(storage_path('app') . '/bishe/student_excel/', $name);
                return $this->importStudent($path);
            }else{
                return $this->toast(0, '文件无效');
            }
        }else{
            return $this->toast(0,'请选择正确的文件类型');
        }
    }
    private function importStudent($excelPath){ // 处理用于上传的excel
        $phpexcel = new \PHPExcel_Reader_Excel2007();
        if(!$phpexcel->canRead($excelPath)){
            return ['msg'=> '不可读,仅支持EXCEL2007及以上的版本','status'=>false];
        }
        $sheet = $phpexcel->load($excelPath)->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $sheetHeader = $sheet->rangeToArray(
            'A1:' . 'B' . 1,
            NULL,TRUE,FALSE
        );
        $sheetbody = $sheet->rangeToArray(
            'A2:' . 'B' . $highestRow,
            NULL,TRUE,FALSE
        );
        if($sheetHeader[0] !== ['姓名','工号',]){
            return $this->toast(0, '输入格式请规范,A1:姓名,A2:工号');
        }
        $insert = [];

        $create_at = date("Y-m-d H:i:s");

        $student = [ // 通用模板
            'created_at'=>$create_at,
            'updated_at'=>$create_at,
            'institute_id'=>$this->institute_id,
        ];
        foreach( $sheetbody as $key=>$item) {
            if($item[0] && $item[1]){
                $student['name'] = $item[0];
                $student['job_num'] = $item[1];
                $student['grade_id'] = $this->grade_id;
                if(Student::where('institute_id',$this->institute_id)->where('job_num', $item[1])->count()){
                    return $this->toast(0, '请检查第'.($key+1) . '条数据,数据库中已存在该学号的学生,请校验');
                }else{
                    $insert[] = $student; // 压入待插入的数组
                }
            }else{
                return $this->toast(0, '请检查第'. ($key+1) . '条数据,数据不能为空');
            }
        }
        $result = Student::insert($insert);
        return $this->toast($result, $result?'数据全部导入成功':'数据插入失败');
    }
}
