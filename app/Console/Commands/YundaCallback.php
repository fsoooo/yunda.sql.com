<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/12/7
 * Time: 15:07
 * 韵达回传信息定时任务（时间没定）
 */
namespace App\Console\Commands;

use App\Helper\LogHelper;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Console\Command;
use App\Helper\IdentityCardHelp;
use Illuminate\Http\Request;
use App\Helper\DoChannelsSignHelp;
use App\Helper\RsaSignHelp;
use App\Helper\AesEncrypt;
use Validator, DB, Image, Schema;
use App\Models\Channel;
use App\Models\UserChannel;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Session,Cache;
use App\Models\Order;
use App\Models\OrderParameter;
use App\Models\WarrantyPolicy;
use App\Models\WarrantyRecognizee;
use App\Models\WarrantyRule;
use \Illuminate\Support\Facades\Redis;
use App\Models\ChannelPrepareInfo;
use App\Models\ChannelOperate;
use App\Models\TimedTask;

class YundaCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yundacallback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'yundacallback Command description';

    /**
     * Create a new command instance.
     * @return void
     * 初始化
     *
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->sign_help = new DoChannelsSignHelp();
        $this->signhelp = new RsaSignHelp();
        $this->request = $request;
    }



    /**
     * 返回给韵达的对账信息
     * @URL  http://ywy.yundasys.com:45170/ywy-outside/interface/ywy/order/getywyinsuranceinfo.do
     */
    public function handle()
    {
       
        set_time_limit(0);//永不超时
        $num = 100;
        $channel_prepare_res = ChannelPrepareInfo::where('operate_time', date('Y-m-d', time() - 24 * 3600))
            ->with(['channelOperateRes' => function ($a) {
                $a->where('operate_time', date('Y-m-d', time() - 24 * 3600));
            }])
            ->paginate($num);
        $pages = $channel_prepare_res->lastPage();
        $totals = $channel_prepare_res->total();
        $url = 'http://ywy.yundasys.com:45170/ywy-outside/interface/ywy/order/getywyinsuranceinfo.do';
        for($page=0;$page<$pages;$page++){
            $channel_res = ChannelPrepareInfo::where('operate_time', date('Y-m-d', time() - 24 * 3600))
                ->with(['channelOperateRes' => function ($a) {
                    $a->where('operate_time', date('Y-m-d', time() - 24 * 3600));
                }])
                ->skip(($page-1)*$num)
                ->take($num)
                ->get();
            $data = [];
            $data['data'] = $channel_res;
            $data['returnType'] = '1';//投保数据
            $data = json_encode($data);
//            dd($data);die;
            $url = 'http://ywy.yundasys.com:45170/ywy-outside/interface/ywy/order/getywyinsuranceinfo.do';
            //封装curl=======================================================================================
//            $response = Curl::to($url)
//                ->returnResponseObject()
//                ->withData($data)
//                ->withHeader("Content-Type:application/json;")
//                ->withTimeout(600)
//                ->get();
            //原生curl=======================================================================================

            $ch = curl_init();//初始化curl
            curl_setopt($ch, CURLOPT_URL, $url);//路径
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POST, true);//post方式提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置提交数据
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $response = curl_exec($ch); //执行选项
            curl_close($ch);
        }

        return 'end';
    }
}


