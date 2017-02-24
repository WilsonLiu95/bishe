<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/


$factory->define(\App\Model\Admin::class, function ($faker) {
    return [
        'institute_id'=>1,
        "account" => 19951995,
        "password" => 19951995,
    ];
});

$factory->define(\App\Model\Major::class, function ($faker) {
    return [
        'name' => $faker->name,
        'institute_id' => 1,
    ];
});$factory->define(\App\Model\Grade::class, function ($faker) {
    return [
        'id'=>1,
        'name' => 2013,
        'status'=> 1,
        'max_create_class'=>6,
        'max_select_class'=>2,
        'institute_id' => 1,

    ];
});$factory->define(\App\Model\Institute::class, function ($faker) {
    return [
        'id' =>1,
        'name' => "电信学院",

    ];
});

$factory->define(\App\Model\Student::class, function ($faker) {
    return [
        "grade_id"=>1,
        "institute_id" =>1,
        "name" => $faker->name,
        "job_num" => $faker->randomNumber($nbDigits = NULL),
        "phone" => $faker->randomNumber,
        "QQ" => $faker->randomNumber,
        "email" => $faker->email,
        "intro" => $faker->text($maxNbChars = 200)
    ];
});

$factory->define(\App\Model\Teacher::class, function ($faker) {
    return [
        'name' => $faker->name,
        "institute_id" =>1,
        'major_id'=>$faker->shuffle([1,2,3,4])[0],
        'job_num' => $faker->randomNumber($nbDigits = NULL) ,
        "phone" => $faker->randomNumber,
        "QQ" => $faker->randomNumber,
        "email" => $faker->email,
        "intro" => $faker->text($maxNbChars = 200)
    ];
});
$factory->define(\App\Model\Message::class, function ($faker) {
    return [
        'from_type'=> $faker->shuffle([0,1,2])[0],
        'send_type'=> $faker->shuffle([0,1,2])[0],
        'from_id' => $faker->numberBetween($min = 1, $max = 100),
        'send_id' => $faker->numberBetween($min = 1, $max = 100),
        'is_read' => $faker->shuffle([0,1])[0],
        'title'=>$faker->word,
        'content' => $faker->text
    ];
});

$factory->define(\App\Model\Schedule::class, function ($faker) {
    return [
        "course_id"=> $faker->numberBetween($min = 1, $max = 100),
        'status' => $faker->shuffle([0,1,2])[0],
        'student_id' => $faker->numberBetween($min = 1, $max = 100),
    ];
});

$factory->define(\App\Model\Course::class, function ($faker) {

    // 将课程状态与审核状态对应
    $status = $faker->shuffle([0,1,2,3])[0];
    if($status !=1){
        $check_status = 2;
    }else{
        $check_status = $faker->shuffle([0,1])[0];
    }
    return [
        'institute_id' => 1,
        'teacher_id' => $faker->numberBetween(1,100),
        'grade_id' => 1,
        'major_id' => $faker->shuffle([1,2,3,4])[0],
        'title'=>$faker->word,
        'status' => $status,
        'details' => $faker->text,

        'check_status'=> $check_status,
        'check_advice'=>$faker->word,
    ];
});