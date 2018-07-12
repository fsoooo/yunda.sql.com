<?php
/**
 * 固定的属性状态
 */

return [
    'order'=>[  //订单状态
//        'all'=>0,   //所有订单，仅仅用来做判断
        'payed'=>1, //已支付
        'unpayed'=>2, //未支付-核保成功
        'fail'=>3,   //支付失败
        'check_ing' =>4,//支付中
//        'check_end' =>5,//支付结束
        'check_error' =>6,//核保错误
        'cancelpayed'=>7,//取消支付
//        'insuring'=>3, //保障中
//        'feedback'=>4,  //待评价
//        'renewal'=>5,   //带续保，已过期


    ],
    //代理平台佣金状态
    'company_brokerage'=>[
        'clear_wait' => 0,  //待结算
        'clear_ing' => 1,   //结算申请中
        'clear_end' => 2, //已结算
    ],
    'message'=>[   //站内信
        'unread' =>0, //未读
        'read' => 1, //已读
    ],
    'cancel_type'=>[   //退保类型
        'hesitation'=>1,  //在犹豫期内
        'out_hesitation'=>0, //不再犹豫期内
    ],

    'policy'=>[//保单状态
        'unenforced'=>0, //待生效
        'insuring'=>1,//保障中
        'lose'=>2,//失效
        'surrender'=>3,//退保
    ],
    //被保人表
    'warranty_recognizee'=>[
        ''=>1, //
        ''=>2,//
        ''=>3,//
        'deleted'=>4,//删除
    ],
    //团险加人表
    'add_recognizee'=>[
        'add'=>1, //刚添加
        'pass'=>2,//通过
        'Npass'=>3,//未通过
        'deleted'=>4,//删除
    ],
    //消息表messages
    'messages'=>[
        'add'=>1, //刚添加
        'send'=>2,//已发送
        'read'=>3,//已读
    ],


];