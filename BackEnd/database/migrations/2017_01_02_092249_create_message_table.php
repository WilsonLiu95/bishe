<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('from_type');
            $table->tinyInteger('send_type');
            $table->tinyInteger('status')->comment("消息状态,0:未读,1:已读");
                        
            $table->integer('from_id');
            $table->integer('send_id');
            $table->string('title', 32);
            $table->string('content', 256);
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
        Schema::drop('message');
    }
}
