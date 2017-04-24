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
            $table->tinyInteger('status')
                ->comment("状态包含 0:已删除,1:待审核,2:互选中,3:互选完成");
            // 这里课程的删除应该用软删除，但是开始做系统的时候不知道软删除，因此这里继续采用一开始的设计
            $table->tinyInteger('check_status')
                ->comment("审查状态, 0: 待审查,1: 审查未通过, 2:审查通过");
            $table->string("check_advice",256)
                ->comment("审查意见");
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
