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

}
