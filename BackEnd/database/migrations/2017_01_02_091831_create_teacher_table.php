<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->char('openid',28);
            $table->integer('institute_id');
            $table->string('name',32);
            $table->string('teacher_num',32);
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
        Schema::drop('teacher');
    }
}
