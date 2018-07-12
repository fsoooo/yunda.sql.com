<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2018/01/30
 * Time: 16:03
 * 韵达投保出单定时任务
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


class YunDaIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yunda_issue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'yunda_issue Command description';

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
		LogHelper::logSuccess(date('Y-m-d H:i:s',time()), 'YD_issue_start');
        $warranty_rule = WarrantyRule::whereHas('warranty_rule_order',function ($a){
            $a->where('status','1');//已支付订单
        })
            ->where('warranty_id',null)
            ->get();
        if(count($warranty_rule)=='0'){
            exit;
        }
        $i = new Issue();
        foreach ($warranty_rule as $value){
            $result = $i->issue($value);
            if(!$result){
                $respose =  json_encode(['status'=>'503','content'=>'出单失败'],JSON_UNESCAPED_UNICODE);
                LogHelper::logError($respose, 'YD_issue_fail_'.$value['union_order_code']);
            }
            ChannelOperate::where('proposal_num',$value['union_order_code'])
                ->update(['issue_status'=>'200']);
            $respose =  json_encode(['status'=>'200','content'=>'出单完成'],JSON_UNESCAPED_UNICODE);
            LogHelper::logSuccess($respose, 'YD_issue_success_'.$value['union_order_code']);
        }
       LogHelper::logSuccess(date('Y-m-d H:i:s',time()), 'YD_issue_end');
    }
}