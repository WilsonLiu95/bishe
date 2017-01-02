<?php
use Illuminate\Http\Request;
use App\Model;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/',['middleware' => 'auth',  function (Request $request) {
//    $users = DB::select('select * from student where id = ?',[1]);
//    var_dump($users);

//    $key = 'user:name:6';
//
//        //将用户名存储到Redis中
//        Redis::set($key,"wilson刘盛");
//
////判断指定键是否存在
//    if(Redis::exists($key)){
//        //根据键名获取键值
//        dd(Redis::get($key));
//    }
//    $request->session()->put('wilson', 'test');
    echo "<pre>";
    $value = App\Model\Message::find(1)->from;
//    $value = $request->session()->all();
    var_dump($value);

}]);

