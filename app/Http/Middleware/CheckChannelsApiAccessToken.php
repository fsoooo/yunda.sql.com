<?php

namespace App\Http\Middleware;

use App\Models\Channel;
use App\Models\ChannelPrepareInfo;
use Closure;
use App\Helper\DoChannelsSignHelp;
use App\Helper\RsaSignHelp;
use App\Helper\LogHelper;

class CheckChannelsApiAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $all = $request->all();
//        LogHelper::logError($all, 'YD_api_token');
//        if($request->header('access-token')){
//            LogHelper::logError($request->header('access-token'), 'YD_access_token');
//            $access_token = $request->header('access-token');
//        }else{
//            $respose =  json_encode(['status'=>'201','content'=>'请携带access-token进行验证'],JSON_UNESCAPED_UNICODE);
//            print_r($respose);die();
//        }
//        //验证授权
//        if(is_null($access_token)){
//            $respose =  json_encode(['status'=>'201','content'=>'请携带access-token进行验证'],JSON_UNESCAPED_UNICODE);
//            print_r($respose);die();
//        }
//        $sign_help = new RsaSignHelp();
//        $access_token_data = json_decode($sign_help->base64url_decode($access_token),true);
////        //token里包含内容
//////        $access_token_data =  [
//////        "channel_code" => 'www.yundaex.com',//渠道标识
//////        "account_id" => "1505187019288846",
//////        "person_code" => '410881199406056514',//用户身份信息
//////        "person_phone" => '15701681524',//用户手机号
//////        "timestamp" => 1505267513，//时间戳
//////        "expiry_date" => 3600,//过期时间
//////        ];
//        if(is_null($access_token_data)){
//            $respose =  json_encode(['status'=>'202','content'=>'授权验证失败！请重新获取'],JSON_UNESCAPED_UNICODE);
//            print_r($respose);die();
//        }
        return $next($request);
    }
}
