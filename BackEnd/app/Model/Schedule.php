<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    protected $table = 'schedule';
    use SoftDeletes;
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = [];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['created_at','updated_at'];
    protected $appends = ['student_name','job_num'];

    public function student()
    {
        return $this->belongsTo('App\Model\Student');
    }
    public function course()
    {
        return $this->belongsTo('App\Model\Course');
    }
    public function getStudentNameAttribute()
    {
        $student = $this->student()->get();
        if(count($student)){
            return $student[0]['name'];
        }else{
            return null;
        }
    }
    public function getJobNumAttribute()
    {
        $student = $this->student()->get();
        if(count($student)){
            return $student[0]['job_num'];
        }else{
            return null;
        }

    }

}
