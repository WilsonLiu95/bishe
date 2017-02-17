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
        //
        $schedule = $this->schedule()->where("status",1);
        $this->student_num = $schedule->count();

        $res = "";
        if($this->status == 2) {
            return $res;
        }
        // 互选完成
        $schedule = $this->schedule()->where("status",$this->status)->get();
        foreach ($schedule as $item){
            $res .= $item->student_name .",";
        }
        $this->student_list = $res;
        return $this;
    }




}
