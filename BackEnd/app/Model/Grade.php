<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Grade extends Model
{
    protected $table = 'grade';
    use SoftDeletes;
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
    protected $dates = ['deleted_at'];
    protected $guarded = ['created_at','updated_at'];
    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
}
