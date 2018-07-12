<?php
/**
 * 各列表 每列显示数目
 */

return [
    'frontend'=>[
        'message' => 10,
        'task' => 10,
        'warranty'=>10,
        'order'=>4,
        'product_list' => 5,
        'plan_lists' => 10,//计划书
        'agent_performance' => 5,//代理人业绩
        'demand' => 10,//代理人工单
        'communication' => 10,//代理人共同记录
    ],
    'backend'=>[
        'roles' => 10, //规则列表每页显示条数
        'permissions' => 10, //权限列表
        'agent' => 10,
        'agent_plan' => 10,
        'ditches' => 10,    //渠道列表
        'cust' => 10,
        'claim' => 10,
        'task'=>10,
        'demand'=>10,
        'maintenance'=>10,
        'order'=>10,
        'cancel'=>10,  //退保列表
        'channel'=>10,
        'product'=>8,//产品列表
        'product_sale'=>10,//产品销售详情
        'brokerage_ditch' => 8, //渠道佣金
        'brokerage_agent' => 8,    //代理人佣金
        'channel_user'=>8,//渠道用户
        'messages'=>1,//消息管理
    ]
];