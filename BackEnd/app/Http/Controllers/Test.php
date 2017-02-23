<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ScheduleTab;
use App\Model\Course;
use App\Model\Schedule;
use App\Model\Student;
use App\Model\Teacher;
use Faker\Factory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

class Test extends Controller
{
    public function getIndex(Request $request)
    {

        return 111;
    }


}
