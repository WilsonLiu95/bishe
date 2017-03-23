<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;

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
		public function makePage($modelString, $option){ // 制表
        $option = $this->option->merge($option);
        $model = app($modelString); // 获取模型
        $handle = $model->where(function($query)use($option){ // search关键词模糊匹配
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
                $handle->whereIn($key, $value);
            }
        };

        $data = $handle->paginate($option['size'], ['*'],'page',$option['page'])->toArray();
        return $data;
    }
}
