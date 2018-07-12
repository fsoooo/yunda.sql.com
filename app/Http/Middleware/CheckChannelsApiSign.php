<?php

namespace App\Http\Middleware;

use App\Models\Channel;
use Closure;
use App\Helper\DoChannelsSignHelp;
use App\Helper\LogHelper;

class CheckChannelsApiSign
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
//        LogHelper::logError($all, 'YD_api_sign');
//        //参数验证
//        if(empty($all['account_id']) || empty($all['timestamp']) || empty($all['biz_content']) || empty($all['sign']))
//            return response('缺少必要参数', 401);
//        unset($all['sign']);
//        krsort($all);
//        //account_id验证
//        $u = Channel::where('only_id', $all['account_id'])->first();
//        if(empty($u))
//            return response('account_id 不存在！', 402);
//        $sign_help = new DoChannelsSignHelp();
//        //签名验证
////        $sign = md5($sign_help->base64url_encode(json_encode($all)) . $u->sign_key);
//        $sign = md5($u->only_id.$all['timestamp'].$u->sign_key);
//        if($sign !== $request->get('sign')){
//            return response('验签失败', 403);
//        }
        return $next($request);
    }
}
