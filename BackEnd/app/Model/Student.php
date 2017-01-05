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

    public function getAccountAttribute(){
         $data =  array(
            "direction" => $this->direction()->get()[0]["name"],
             "institute" =>$this->institute()->get()[0]["name"]
         );
        $orign = $this->all()->toArray()[0];

        $orign = array_except($orign,["id","openid","created_at","updated_at","direction_id","institute_id"]);
        return array_merge($orign,$data);
    }

    // 关联
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function direction()
    {
        return $this->belongsTo('App\Model\Direction');
    }

    public function schedule()
    {
        return $this->hasMany('App\Model\Schedule');
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
