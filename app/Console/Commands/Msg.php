<?php
/**
 * Created by PhpStorm.
 * User: cyt
 * Date: 2017/12/4
 * Time: 15:07
 */
namespace App\Console\Commands;
use App\Models\Messages;
use Illuminate\Console\Command;

class Msg extends Command{
    protected $signature = 'msg';

    //定时发送消息
    public function sendMsg()
    {
        $data = Messages::where('status',1)
            ->get();
        foreach($data as $v){

        }
    }
}