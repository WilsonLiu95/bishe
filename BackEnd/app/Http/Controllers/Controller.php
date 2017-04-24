<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;
    private $option;
    public function __construct()
    {
        $this->option = collect([
            'search'=>[
                // 'key'=> ['job_num','name'], // orwhere 查询
                // 'rule'=> '759',
            ],
            'filter'=>[
//                 'direction_id'=>[1,2,3],
                // 'major_id'=>[1,3],
            ],
            'orderBy'=> [
                'key'=>'id',
                'order'=>'asc'
            ],
            'size'=> 20,
            'page'=> 1,
            'where'=>[
                ['institute_id', '=', 1], // where筛选
            ],
            'col'=>'*', // 搜索哪些字段值,为数组
        ]);
    }
	public function json($state=1,$data=array()){
		$res = array(
		            "state"=>$state,
		            "data"=>$data
		        );
		return response()->json($res);
	}
	public function redirect($option=array(), $msg="",$data = array()){
		// 如果要填路径则$option为路径
				return response()->json([
				            "state" => 301,
				            "option"=>$option,
                            'msg'=>$msg,
							'data'=> $data]);
	}
	public function toast($state=1,$msg="",$data=array()){
		$toast = array(
		            "state"=>$state,
		            "msg"=>$msg,
		            "data"=>$data,
		
		);
		return response()->json($toast);
	}

    public function makePage($modelString, $optionOrign){ // 制表

        $option = $this->option->merge($optionOrign);
        $handle = $this->makePageHandle($modelString, $option);
        $data = $handle->paginate($option['size'], ['*'],'page',$option['page'])->toArray();
        return $data;
    }
    public function makePageHandle($modelString, $option){
        $option = $this->option->merge($option);
        $model = app($modelString); // 获取模型
        // 经验证必须加入一道操作,否则后面的操作都不会生效,所以将字段select加在此处
        $handle = $model->select($option['col']);
        foreach($option['where'] as $item){
            $handle->where($item[0],$item[1],$item[2]);
        }
        $handle->where(function($query)use($option){ // search关键词模糊匹配
            if($option['search'] && $option['search']['rule']){ // 搜索规则
                foreach ($option['search']['key'] as $key=>$item) {
                    if($key==0){
                        $query->where($item,'like','%'. $option['search']['rule'] .'%');
                    }else{
                        $query->orWhere($item,'like','%'. $option['search']['rule'] .'%');
                    }
                };

            }
        });
        if(count($option['orderBy'] )&& $option['orderBy']['key'] && $option['orderBy']['order']) { // 排序
            $handle->orderBy($option['orderBy']['key'], $option['orderBy']['order']);
        }
        // 过滤filter

        if(count($option['filter'])){
            foreach($option['filter'] as $key=>$value){
                if($value){
                    $handle->whereIn($key, $value);
                }

            }
        };
        return $handle;
    }
}
