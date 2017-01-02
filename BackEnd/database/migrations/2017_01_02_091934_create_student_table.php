<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student', function (Blueprint $table) {
            $table->increments('id');
            $table->char('openid',28);
            $table->char('grade',4);
            $table->integer('institute_id');
            $table->integer('direction_id');
            $table->string('name',32);
            $table->string('student_num',32);
            $table->char('phone',11);
            $table->string('qq',16);
            $table->string('email',32);
            $table->string('intro',256);
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
        Schema::drop('student');
    }
}
