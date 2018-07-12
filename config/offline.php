<?php


return [

    /*
    |--------------------------------------------------------------------------
    |  policy offline import attribute
    |--------------------------------------------------------------------------
    */

    'policyOfflineAttribute' => [
        'product_name' => '产品名称',
        'product_insure_type' => '产品类型',
        'product_category_name' => '产品分类',
        'product_company_name' => '所属保险公司',
        'product_base_stages_way' => '缴费期限',
        'product_base_price' => '保费',
        'product_main_insure' => '主险',

        'ditch_name' => '渠道名称',
        'market_ditch_rate' => '渠道佣金比例',

        'agent_name' => '代理人姓名',
        'agent_code' => '代理人身份证号',
        'agent_phone' => '代理人手机号',
        'agent_email' => '代理人邮箱',
        'agent_job_number' => '代理人工号',
        'market_agent_rate' => '代理人佣金比例',

        'warranty_code' => '保单号',
        'warranty_start_time' => '保障开始时间',
        'warranty_end_time' => '保障结束时间',
        'order_pay_time' => '签订时间',
        'warranty_status' => '保单状态',

        'policy_name' => '投保人姓名',
        'policy_code' => '投保人身份证号',
        'policy_phone' => '投保人联系方式',
        'policy_email' => '投保人邮箱地址',
        'policy_other' => '投保人其他信息',
        'policy_company_name' => '投保企业名称',
        'policy_is_three_company' => '投保企业是否是三证合一企业（是／否）',
        'policy_all_code' => '投保企业统一信用代码',
        'policy_organization_code' => '投保企业组织机构代码',
        'policy_license_code' => '投保企业营业执照编号',
        'policy_tax_code' => '投保企业纳税人识别号',
        'policy_street_address' => '投保企业所在地址',
        'policy_license_image' => '投保企业营业执照照片',

        'recognize_name' => '被保人姓名',
        'recognize_code' => '被保人身份证号',
        'recognize_phone' => '被保人联系方式',
        'recognize_email' => '被保人邮箱地址',
        'recognize_other' => '被保人其他信息',
        'recognize_company_name' => '被保企业名称',
        'recognize_is_three_company' => '被保企业是否是三证合一企业（是／否）',
        'recognize_all_code' => '被保企业统一信用代码',
        'recognize_organization_code' => '被保企业组织机构代码',
        'recognize_license_code' => '被保企业营业执照编号',
        'recognize_tax_code' => '被保企业纳税人识别号',
        'recognize_street_address' => '被保企业所在地址',
        'recognize_license_image' => '被保企业营业执照照片',

        'image1'=>'照片1',
        'image2'=>'照片2',
        'image3'=>'照片3',
        'image4'=>'照片4',
        'image5'=>'照片5',
        'image6'=>'照片6',
        'image7'=>'照片7',
        'image8'=>'照片8',
        'image9'=>'照片9',
        'image10'=>'照片10',
    ],

    /*
    |--------------------------------------------------------------------------
    | import excel image line
    |--------------------------------------------------------------------------
    */

    'imageExcelLine' => [
        '32'=>'AG',
        '45'=>'AT',
        '46'=>'AU',
        '47'=>'AV',
        '48'=>'AW',
        '49'=>'AX',
        '50'=>'AY',
        '51'=>'AZ',
        '52'=>'BA',
        '53'=>'BB',
        '54'=>'BC',
        '55'=>'BD',
    ],

    /*
    |--------------------------------------------------------------------------
    | import file maximum size
    |--------------------------------------------------------------------------
    */

    'fileSize' => 1024,

    /*
    |--------------------------------------------------------------------------
    | import file type
    |--------------------------------------------------------------------------
    */

    'fileType' => [
        'xlsx',
        'xls'
    ],

    /*
    |--------------------------------------------------------------------------
    | import example file url
    |--------------------------------------------------------------------------
    */

    'exampleUrl' => 'public/download/offline_example.xlsx',

];
