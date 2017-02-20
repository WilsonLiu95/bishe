<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $table = 'student';

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

    public function account(){
        $orign = $this->toArray();
        $orign['institute'] = $this->institute()->first()["name"];
        $orign = array_except($orign,["id","openid","created_at","updated_at","direction_id","institute_id"]);
        return $orign;
    }

    // 关联
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
    public function schedule()
    {
        return $this->hasMany('App\Model\Schedule');
    }
    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }
    public function message_from()
    {
        return $this->morphMany('App\Model\Message','from');
    }
    public function message_send()
    {
        return $this->morphMany('App\Model\Message','send');
    }

}
