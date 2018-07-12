<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2018/01/16
 * Time: 16:03
 * 韵达投保微信代扣定时任务（凌晨四点到十点）
 */
namespace App\Console\Commands;

use Illuminate\Http\Request;
use App\Helper\DoChannelsSignHelp;
use App\Helper\RsaSignHelp;
use App\Helper\AesEncrypt;
use Ixudra\Curl\Facades\Curl;
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
use App\Models\ChannelContract;
use App\Models\TimedTask;
use App\Helper\LogHelper;
use App\Helper\IdentityCardHelp;
use App\Helper\Issue;
use App\Models\Warranty;
use App\Models\UserChannels;
use App\Models\OrderBrokerage;
use App\Models\Product;
use App\Models\ApiInfo;
use App\Models\Bank;
use App\Models\UserBank;
use App\Models\Competition;
use App\Models\CompanyBrokerage;
use App\Models\OrderPrepareParameter;
use App\Models\ChannelClaimApply;
use App\Models\ChannelInsureInfo;
use App\Helper\UploadFileHelper;
use Illuminate\Console\Command;


class YunDaPre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yunda_pre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'yunda_pre Command description';

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
     *
     * 预投保信息处理
     * 出队，变形，投保，入库
     * todo  定时任务，处理信息（出队，变形，投保，入库）
     */
    public function handle()
    {
        $count = Redis::Llen('prepare_info');
        if($count<1){
            exit;
        }
        set_time_limit(0);//永不超时
        echo '处理开始时间'.date('Y-m-d H:i:s', time()).'<br/>';
        $file_area = "/var/www/html/yunda.inschos.com/public/Tk_area.json";
        $file_bank = "/var/www/html/yunda.inschos.com/public/Tk_bank.json";
        $json_area = file_get_contents($file_area);
        $json_bank = file_get_contents($file_bank);
        $area = json_decode($json_area,true);
        $bank = json_decode($json_bank,true);
        for($i=0;$i<$count;$i++) {
            $value = json_decode(base64_decode(Redis::lpop('prepare_info')),true);
            foreach($value as $key=>$item){//每次1000条数据
                if(key_exists($item['channel_provinces'],$area)) {
                    $item['channel_provinces'] = $area[$item['channel_provinces']];
                }
                if(key_exists($item['channel_city'],$area)){
                    $item['channel_city'] = $area[$item['channel_city']];
                }
                if(key_exists($item['channel_county'],$area)){
                    $item['channel_county'] = $area[$item['channel_county']];
                }
                if(key_exists($item['channel_bank_name'],$bank)){
                    $item['channel_bank_name'] = $bank[$item['channel_bank_name']];
                }
                $item['operate_time'] = date('Y-m-d',time());
                //预投保操作，批量操作（定时任务）
                $idCard_status = IdentityCardHelp::getIDCardInfo($item['channel_user_code']);
                if($idCard_status['status']=='2') {
                    //TODO 判断是否已经投保
                    $channel_insure_res = ChannelOperate::where('channel_user_code',$item['channel_user_code'])
                        ->where('operate_time',$item['operate_time'])
                        ->where('prepare_status','200')
                        ->select('proposal_num')
                        ->first();
                    //已经投保的，不再投保
                    if(!empty($channel_insure_res)){
                        return 'end';
                    }
                    $insure_status = $this->doInsurePrepare($item);
                    $item['operate_code'] = '实名信息正确,预投保成功';
                }else{
                    $item['operate_code'] = '实名信息出错:身份证号';
                }
                ChannelPrepareInfo::insert($item);
            }
        }
        LogHelper::logChannelSuccess($count, 'YD_prepara_ok');
    }


    /**
     * 预投保操作
     *
     */
    public function doInsurePrepare($prepare){
        $data = [];
        $insurance_attributes = [];
        $base = [];
        $base['ty_start_date'] = $prepare['operate_time'];
        $toubaoren = [];
        $toubaoren['ty_toubaoren_name'] = $prepare['channel_user_name'];//投保人姓名
        $toubaoren['ty_toubaoren_id_type'] = $prepare['channel_user_type']??'1';//证件类型
        $toubaoren['ty_toubaoren_id_number'] = $prepare['channel_user_code'];//证件号
        $toubaoren['ty_toubaoren_birthday'] = substr($toubaoren['ty_toubaoren_id_number'],6,4).'-'.substr($toubaoren['ty_toubaoren_id_number'],10,2).'-'.substr($toubaoren['ty_toubaoren_id_number'],12,2);
        if(substr($toubaoren['ty_toubaoren_id_number'],16,1)%2=='0'){
            $toubaoren['ty_toubaoren_sex'] = '女';
        }else{
            $toubaoren['ty_toubaoren_sex'] = '男';
        }
        $toubaoren['ty_toubaoren_phone'] = $prepare['channel_user_phone'];
        $toubaoren['ty_toubaoren_email'] = $prepare['channel_user_email'];
        $toubaoren['ty_toubaoren_provinces'] = $prepare['channel_provinces'];
        $toubaoren['ty_toubaoren_city'] = $prepare['channel_city'];
        $toubaoren['ty_toubaoren_county'] = $prepare['channel_county'];
        $toubaoren['channel_user_address'] = $prepare['channel_user_address'];
        $toubaoren['courier_state'] = $prepare['courier_state'];
        $toubaoren['courier_start_time'] = $prepare['courier_start_time'];
        $beibaoren = [];
        $beibaoren[0]['ty_beibaoren_name'] = $prepare['channel_user_name'];
        $beibaoren[0]['ty_relation'] = '1';//必须为本人
        $beibaoren[0]['ty_beibaoren_id_type'] = $prepare['channel_user_type']??'1';
        $beibaoren[0]['ty_beibaoren_id_number'] = $prepare['channel_user_code'];
        $beibaoren[0]['ty_beibaoren_birthday'] = substr($toubaoren['ty_toubaoren_id_number'],6,4).'-'.substr($toubaoren['ty_toubaoren_id_number'],10,2).'-'.substr($toubaoren['ty_toubaoren_id_number'],12,2);
        if(substr($toubaoren['ty_toubaoren_id_number'],16,1)%2=='0'){
            $beibaoren[0]['ty_beibaoren_sex'] = '女';
        }else{
            $beibaoren[0]['ty_beibaoren_sex'] = '男';
        }
        $beibaoren[0]['ty_beibaoren_phone'] = $prepare['channel_user_phone'];
        $insurance_attributes['ty_base'] = $base;
        $insurance_attributes['ty_toubaoren'] = $toubaoren;
        $insurance_attributes['ty_beibaoren'] = $beibaoren;
        $data['price'] = '2';
        $data['private_p_code'] = 'VGstMTEyMkEwMUcwMQ';
        $data['quote_selected'] = '';
        $data['insurance_attributes'] = $insurance_attributes;
        $data = $this->signhelp->tySign($data);
        //发送请求
        $response = Curl::to(env('TY_API_SERVICE_URL') . '/ins_curl/buy_ins')
            ->returnResponseObject()
            ->withData($data)
            ->withTimeout(60)
            ->post();
        if($response->status != 200){
            ChannelOperate::insert([
                'channel_user_code'=>$prepare['channel_user_code'],
                'prepare_status'=>'500',
                'prepare_content'=>$response->content,
                'operate_time'=>date('Y-m-d',time()),
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time())
            ]);
            $content = $response->content;
            $return_data =  json_encode(['status'=>'501','content'=>$content],JSON_UNESCAPED_UNICODE);
            print_r($return_data);
        }
        $prepare['parameter'] = '0';
        $prepare['private_p_code'] = 'VGstMTEyMkEwMUcwMQ';
        $prepare['ty_product_id'] = 'VGstMTEyMkEwMUcwMQ';
        $prepare['agent_id'] = '0';
        $prepare['ditch_id'] = '0';
        $prepare['user_id'] = $prepare['channel_user_code'];
        $prepare['identification'] = '0';
        $prepare['union_order_code'] = '0';
        $return_data = json_decode($response->content, true);
        //todo  本地订单录入
        $add_res = $this->addOrder($return_data, $prepare,$toubaoren);
        if($add_res){
            $return_data =  json_encode(['status'=>'200','content'=>'投保完成'],JSON_UNESCAPED_UNICODE);
            print_r($return_data);
        }
    }

    /**
     * 对象转化数组
     *
     */
    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }

    /**
     * 添加投保返回信息
     *
     */
    protected function addOrder($return_data, $prepare, $policy_res)
    {
        try{
            //查询是否在竞赛方案中
            $private_p_code = $prepare['private_p_code'];
            $competition_id = 0;
            $is_settlement = 0;
            $ditch_id = $prepare['ditch_id'];
            $agent_id = $prepare['agent_id'];
            //订单信息录入
            foreach ($return_data['order_list'] as $order_value){
                $order = new Order();
                $order->order_code = $order_value['union_order_code']; //订单编号
                $order->user_id = isset($_COOKIE['user_id'])?$_COOKIE['user_id']:' ';//用户id
                $order->agent_id = $agent_id;
                $order->competition_id = $competition_id;//竞赛方案id，没有则为0
                $order->private_p_code = $private_p_code;
                $order->ty_product_id = $prepare['ty_product_id'];
                $order->start_time = isset($order_value['start_time'])?$order_value['start_time']: ' ';
                $order->claim_type = 'online';
                $order->deal_type = 0;
                $order->is_settlement = $is_settlement;
                $order->premium = $order_value['premium'];
                $order->status = config('attribute_status.order.unpayed');
                $order->pay_way = json_encode($return_data['pay_way']);
                $order->save();
            }
            //投保人信息录入
            $warrantyPolicy = new WarrantyPolicy();
            $warrantyPolicy->name = isset($policy_res['ty_toubaoren_name'])?$policy_res['ty_toubaoren_name']:'';
            $warrantyPolicy->card_type = isset($policy_res['ty_toubaoren_id_type'])?$policy_res['ty_toubaoren_id_type']:'';
            $warrantyPolicy->occupation = isset($policy_res['ty_toubaoren_occupation'])?$policy_res['ty_toubaoren_occupation']:'';//投保人职业？？
            $warrantyPolicy->code = isset($policy_res['ty_toubaoren_id_number'])?$policy_res['ty_toubaoren_id_number']:'';
            $warrantyPolicy->phone =  isset($policy_res['ty_toubaoren_phone'])?$policy_res['ty_toubaoren_phone']:'';
            $warrantyPolicy->email =  isset($policy_res['ty_toubaoren_email'])?$policy_res['ty_toubaoren_email']:'';
            $warrantyPolicy->area =  isset($policy_res['ty_toubaoren_area'])?$policy_res['ty_toubaoren_area']:'';
            $warrantyPolicy->status = config('attribute_status.order.check_ing');
            $warrantyPolicy->save();
            //用户信息录入
            $user_check_res  = User::where('code',$policy_res['ty_toubaoren_id_number'])
                ->where('phone',$policy_res['ty_toubaoren_phone'])
                ->first();
            if(empty($user_check_res)){
                $user_res = new User();
                $user_res->name = isset($policy_res['ty_toubaoren_name'])?$policy_res['ty_toubaoren_name']:'';
                $user_res->real_name = isset($policy_res['ty_toubaoren_name'])?$policy_res['ty_toubaoren_name']:'';
                $user_res->phone = isset($policy_res['ty_toubaoren_phone'])?$policy_res['ty_toubaoren_phone']:'';
                $user_res->code = isset($policy_res['ty_toubaoren_id_number'])?$policy_res['ty_toubaoren_id_number']:'';
                $user_res->email =  isset($policy_res['ty_toubaoren_email'])?$policy_res['ty_toubaoren_email']:'';
                $user_res->occupation = isset($policy_res['ty_toubaoren_occupation'])?$policy_res['ty_toubaoren_occupation']:'';
                $user_res->address = isset($policy_res['ty_toubaoren_area'])?$policy_res['ty_toubaoren_area']:'';
                $user_res->type = 'user';
                $user_res->password = bcrypt('123qwe');
            }

            //被保人信息录入
            foreach ($return_data['order_list'] as $recognizee_value){
                $warrantyRecognizee = new WarrantyRecognizee();
                $warrantyRecognizee->name = $recognizee_value['name'];
                $warrantyRecognizee->order_id = $order->id;
                $warrantyRecognizee->order_code = $recognizee_value['out_order_no'];
                $warrantyRecognizee->relation = $recognizee_value['relation'];
                $warrantyRecognizee->occupation =isset($recognizee_value['occupation'])?$recognizee_value['occupation']: '';
                $warrantyRecognizee->card_type = isset($recognizee_value['card_type'])?$recognizee_value['card_type']: '';
                $warrantyRecognizee->code = isset($recognizee_value['card_id'])?$recognizee_value['card_id']: '';
                $warrantyRecognizee->phone = isset($recognizee_value['phone'])?$recognizee_value['phone']: '';
                $warrantyRecognizee->email = isset($recognizee_value['email'])?$recognizee_value['email']: '';
                $warrantyRecognizee->start_time = isset($recognizee_value['start_time'])?$recognizee_value['start_time']: '';
                $warrantyRecognizee->end_time = isset($recognizee_value['end_time'])?$recognizee_value['end_time']: '';
                $warrantyRecognizee->status = config('attribute_status.order.unpayed');
                $warrantyRecognizee->save();
                //用户信息录入
                $user_check_res  = User::where('code',$recognizee_value['card_id'])
                    ->where('real_name',$recognizee_value['name'])
                    ->first();
                if(empty($user_check_res)){
                    $user_res = new User();
                    $user_res->name = $recognizee_value['name'];
                    $user_res->real_name = $recognizee_value['name'];
                    $user_res->phone = isset($recognizee_value['phone'])?$recognizee_value['phone']: '';
                    $user_res->code = isset($recognizee_value['card_id'])?$recognizee_value['card_id']: '';
                    $user_res->email =  isset($recognizee_value['email'])?$recognizee_value['email']: '';
                    $user_res->occupation = isset($recognizee_value['occupation'])?$recognizee_value['occupation']: '';
                    $user_res->address =isset($recognizee_value['address'])?$recognizee_value['address']: '';
                    $user_res->type = 'user';
                    $user_res->password = bcrypt('123qwe');
                }
            }
            //添加投保参数到参数表
            $orderParameter = new OrderParameter();
            $orderParameter->parameter = $prepare['parameter'];
            $orderParameter->order_id = $order->id;
            $orderParameter->ty_product_id = $order->ty_product_id;
            $orderParameter->private_p_code = $private_p_code;
            $orderParameter->save();
            //添加到关联表记录
            $WarrantyRule = new WarrantyRule();
            $WarrantyRule->agent_id = $agent_id;
            $WarrantyRule->ditch_id = $ditch_id;
            $WarrantyRule->order_id = $order->id;
            $WarrantyRule->ty_product_id = $order->ty_product_id;
            $WarrantyRule->premium = $order->premium;
            $WarrantyRule->union_order_code = $return_data['union_order_code'];//总订单号
            $WarrantyRule->parameter_id = $orderParameter->id;
            $WarrantyRule->policy_id = $warrantyPolicy->id;
            $WarrantyRule->private_p_code = $private_p_code;   //预留
            $WarrantyRule->save();
            //添加到渠道用户操作表
            $ChannelOperate = new ChannelOperate();
            $ChannelOperate->channel_user_code = $policy_res['ty_toubaoren_id_number'];
            $ChannelOperate->order_id = $order->id;
            $ChannelOperate->proposal_num = $return_data['union_order_code'];
            $ChannelOperate->prepare_status = '200';
            $ChannelOperate->operate_time = date('Y-m-d',time());
            $ChannelOperate->save();
            DB::commit();
            return true;
        }catch (\Exception $e)
        {
            DB::rollBack();
            LogHelper::logChannelError([$return_data, $prepare], $e->getMessage(), 'addOrder');
            return false;
        }
    }
}