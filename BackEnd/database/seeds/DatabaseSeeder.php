<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(\App\Model\Institute::class)->create();
        factory(\App\Model\Grade::class)->create();
        factory(\App\Model\Major::class,4)->create();
        factory(\App\Model\Admin::class)->create();


        factory(\App\Model\Teacher::class,100)->create();

        factory(\App\Model\Message::class,1000)->create();

        factory(\App\Model\Course::class,1000)->create()->each(function($course){
            $schedule = factory(\App\Model\Schedule::class)->make();
            $course->schedule()->save($schedule);
        });

        factory(\App\Model\Student::class,100)->create();
        Model::reguard();
    }
}
