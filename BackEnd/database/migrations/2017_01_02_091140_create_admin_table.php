<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("institute_id");
            $table->char("current_grade",4);
            $table->string("history_grade",256);
            $table->string("account",32);
            $table->string("password",32);
            $table->tinyInteger("system_status");
            $table->tinyInteger("select_max_num");
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
        Schema::drop('admin');
    }
}
