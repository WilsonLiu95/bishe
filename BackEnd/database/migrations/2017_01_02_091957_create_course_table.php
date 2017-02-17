<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');
            $table->integer('grade_id');
            $table->integer('institute_id');
            $table->integer('major_id');
            $table->string('title',32);
            $table->tinyInteger('status'); // 状态包含 0:已删除,1:待审核,2:互选中,3:互选完成
            $table->string('details',512);
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
        Schema::drop('course');
    }
}
