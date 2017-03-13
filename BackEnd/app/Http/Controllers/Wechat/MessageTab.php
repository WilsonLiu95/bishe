<?php

namespace App\Http\Controllers\Wechat;

use App\Model\Message;
use App\Http\Controllers\Wechat\BaseTrait;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MessageTab extends Controller
{
    use BaseTrait;
    public function getIndex(){
        $seg = request()->seg;
        $one_page = 10;
        $msg = $this->getMessage()->skip($seg * $one_page)->take($one_page)->get();
        return $this->json(1,$msg);
    }
    public function getUnreadNumber(){
        $count = $this->getMessage()->where("is_read",false)->count();
        return $this->json(1,$count);
    }
    public function getReadOneMsg(){
        // 必须首先鉴定消息是否属于该用户
        $message =$this->getMessage()->where("id",[request()->id]);
        if($message->exists()){
            $message->update(['is_read'=>1]);
            return $this->json(1);

        }else{
            return $this->toast(0,"数据错误");
        }
    }
    private function  getMessage(){
        // 统一获取message信息,以保证排序等相同
        $send_type = $this->getSessionInfo("isTeacher") ? 1:2;
        $message = Message::where("send_type",$send_type)
            ->where("send_id",$this->getSessionInfo("id"))
            ->orderBy("created_at","ASC");
        return $message;
}
}
