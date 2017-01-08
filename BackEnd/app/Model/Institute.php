<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $table = 'institute';

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
        return $this->hasMany('App\Model\Teacher');
    }
    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
    public function grade()
    {
        return $this->hasMany('App\Model\Grade');
    }
    public function major()
    {
        return $this->hasMany('App\Model\Major');
    }
}
