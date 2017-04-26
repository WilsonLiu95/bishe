<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id');
            $table->string('student_id',32);
            $table->boolean('is_finish_select'); // 是否被老师确认为互选
//            $table->tinyInteger('status'); // 0为选定后退选课程,1为学生选定该课程，2为互选成功，
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('schedule');
    }
}
