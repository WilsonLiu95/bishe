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
    protected $appends = ['teacher', "teacher_phone","student_num","student_list"];
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
//
    public function getTeacherAttribute(){
        $id = $this->teacher_id;
        return Teacher::find($id)->name;
    }
    public function getTeacherPhoneAttribute(){
        $id = $this->teacher_id;
        return Teacher::find($id)->phone;
    }
    public function getStudentNumAttribute(){
        $schedule = $this->schedule()->where("status",1);
        return $schedule->count();
    }
    public function getStudentListAttribute(){
        $res = "";
        if($this->status == 2) {
            return $res;
        }
        // 互选完成
        $schedule = $this->schedule()->where("status",$this->status)->get();
        foreach ($schedule as $item){
            $res .= $item->student_name .",";
        }
        return $res;
    }


}
