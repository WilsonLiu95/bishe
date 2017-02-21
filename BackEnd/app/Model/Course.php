<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'course';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['created_at','updated_at'];
//    关联关系
    public function teacher()
    {
        return $this->belongsTo('App\Model\Teacher');
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function major()
    {
        return $this->belongsTo('App\Model\Major');
    }
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
    public function schedule()
    {
        return $this->hasMany('App\Model\Schedule');
    }

    public function detail(){
        // teacher信息

        $id = $this->teacher_id;
        $tc = Teacher::find($id);
        $this->teacher = $tc->name;
        $this->teacher_phone = $tc->phone;

        $this->student_num = 0; // 设置默认为0
        $this->student_list = "";

        // 课程 状态包含 0:已删除,1:待审核,2:互选中,3:互选完成
        if($this->status==2){
            // schedule状态包含 0:为选定后退选课程,1:为学生选定该课程，2:为互选成功，
            $schedule = $this->schedule()->where("status",1)->get();
            $this->student_num = $schedule->count();

            $schedule->each(function ($item){
                $this->student_list  .= $item->student_name .",";
            });
        }else if ($this->status==3){
            $sc = $this->schedule()->where("status",2);
            if($sc->exists()){ // 如果存在
                $this->student_list = $sc->first()->student_name;
            }else{
                // 出错情况
                $this->student_list  = "数据错误";
            }

        }
        return $this;
    }




}
