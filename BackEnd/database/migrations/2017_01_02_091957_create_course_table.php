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
            $table->char('grade',4);
            $table->integer('institute_id');
            $table->integer('direction_id');
            $table->string('title',32);
            $table->tinyInteger('status');
            $table->tinyInteger('need');
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
