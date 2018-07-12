<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2018/01/16
 * Time: 16:03
 * 韵达投保微信代扣定时任务（凌晨四点到十点）
 */
namespace App\Console\Commands;

use App\Models\ChannelPrepareInfo;
use App\Models\Warranty;
use Illuminate\Http\Request;
use App\Helper\DoChannelsSignHelp;
use App\Helper\RsaSignHelp;
use App\Helper\AesEncrypt;
use Ixudra\Curl\Facades\Curl;
use Validator, DB, Image, Schema;
use App\Models\Channel;
use App\Models\ChannelOperate;
use App\Models\UserChannel;
use App\Models\UserChannels;
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
use App\Models\OrderBrokerage;
use App\Helper\LogHelper;
use App\Models\Product;
use App\Models\ApiInfo;
use App\Models\Bank;
use App\Models\UserBank;
use App\Models\Competition;
use App\Models\CompanyBrokerage;
use App\Models\OrderPrepareParameter;
use App\Models\ChannelClaimApply;
use App\Models\ChannelInsureInfo;
use App\Helper\Issue;
use App\Helper\UploadFileHelper;
use App\Helper\IdentityCardHelp;
use App\Models\ChannelContract;
use Illuminate\Console\Command;


class YunDaPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yunda_pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'yunda_pay Command description';

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
        set_time_limit(0);//永不超时
    }


    /**
     * 微信代扣支付
     * 定时任务，跑支付
     */
    public function handle()
    {
        set_time_limit(0);//永不超时
		LogHelper::logPay(date('Y-m-d H:i:s',time()), 'YD_pay_start');
        $channel_contract_info = ChannelContract::where('is_valid','0')//有效签约
        ->where('is_auto_pay','0')
            ->select('openid','contract_id','contract_expired_time','channel_user_code')
            //openid,签约协议号,签约过期时间,签约人身份证号
            ->get();
        //循环请求，免密支付
        foreach ($channel_contract_info as $value){
            $person_code  = $value['channel_user_code'];
            $channel_res = ChannelOperate::where('channel_user_code',$person_code)
                ->where('prepare_status','200')//预投保成功
                ->where('operate_time',date('Y-m-d',time()-24*3600))//前一天的订单
                ->where('is_work','1')//已上工
                ->select('proposal_num')
                ->first();
            $union_order_code = $channel_res['proposal_num'];
            $data = [];
            $data['price'] = '2';
            $data['private_p_code'] = 'VGstMTEyMkEwMUcwMQ';
            $data['quote_selected'] = '';
            $data['insurance_attributes'] = '';
            $data['union_order_code'] = $union_order_code;
            $data['pay_account'] = $value['openid'];
            $data['contract_id'] = $value['contract_id'];
            $data = $this->signhelp->tySign($data);
            //发送请求
            $response = Curl::to(env('TY_API_SERVICE_URL') . '/ins_curl/wechat_pay_ins')
                ->returnResponseObject()
                ->withData($data)
                ->withTimeout(60)
                ->post();
            // print_r($response);die;
            if($response->status != 200){
				LogHelper::logPay($person_code,$response->content??"",'YD_pay_fail');
                ChannelOperate::where('channel_user_code',$person_code)
                    ->where('proposal_num',$union_order_code)
                    ->update(['pay_status'=>'500','pay_content'=>$response->content]);
                //TODO 签约链接失效（业务员自己取消签约了）
                //TODO 网络延迟等错误，没有判断
//                ChannelContract::where('channel_user_code',$person_code)
//                     ->update([
//                         'is_valid'=>1,//签约失败
//                     ]);
            }
			LogHelper::logPay($person_code, 'YD_pay_ok_'.$union_order_code);
			try{
            $return_data =  json_decode($response->content,true);//返回数据
            //TODO  可以改变订单表的状态
            ChannelOperate::where('channel_user_code',$person_code)
                ->where('proposal_num',$union_order_code)
                ->update(['pay_status'=>'200']);
            WarrantyRule::where('union_order_code',$union_order_code)
                ->update(['status'=>'1']);
            Order::where('order_code',$union_order_code)
                ->update(['status'=>'1']);
			DB::commit();
			LogHelper::logPay(date('Y-m-d H:i:s',time()), 'YD_pay_end_'.$person_code);
			}catch (\Exception $e){
				DB::rollBack();					
				return false;
			}
        }
    }
}