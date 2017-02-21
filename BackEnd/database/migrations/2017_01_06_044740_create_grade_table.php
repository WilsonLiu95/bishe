<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institute_id');

            $table->string('name',32);

            // 个性化配置
            $table->tinyInteger('status')
                ->comment("状态包含 0:关闭,1:当前学年");
            $table->integer('max_create_class')
                ->comment("单个老师最多创建的课程数量");
            $table->integer('max_select_class')
                ->comment("单个学生最多选择的课程数量");

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
        Schema::drop('grade');
    }
}
