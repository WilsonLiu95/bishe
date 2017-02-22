<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teacher';

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


    // get与set
    public function account(){
        $data =  array(
            "major" =>$this->major()->first()["name"],
            "institute" =>$this->institute()->first()["name"]
        );
        $orign = $this->toArray();

        $orign = array_except($orign,["id","openid","created_at","updated_at","direction_id","institute_id"]);
        return array_merge($orign,$data);
    }
    // 关联
    public function  isMajorAdmin(){
        return $this->is_admin;
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function major()
    {
        return $this->belongsTo('App\Model\Major');
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
