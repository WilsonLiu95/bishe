<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedule';

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
        return $this->student()->get()[0]['name'];
    }
    public function getJobNumAttribute()
    {
        return $this->student()->get()[0]['job_num'];
    }

}
