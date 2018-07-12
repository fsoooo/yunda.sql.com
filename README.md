## 数据库更新日志
2017.09.29+
#### 表名称 com_company_brokerage
#### 迁移文件 2017_06_23_094026_create_com_company_brokerage.php
- 操作：添加  字段：ty_product_id   作用：关联产品表  原类型:int   目标类型：无    默认值：        原因：方便分组查询 每款产品 公司所获得佣金


2017.10.10
#### com_cust
#### 迁移文件 2017_05_05_093821_create_com_cust.php
- 操作：添加  字段：occupation  作用：添加用户的职业        原类型:无   目标类型：varchat    默认值：        原因：前端需要展示
- 操作：添加  字段：type  作用：区分客户的类型        原类型:无   目标类型：int    默认值：        原因：移动端页面需要展示
- 操作：添加  字段：id_type  作用：用户证件类型        原类型:无   目标类型：int    默认值：        原因：移动端页面需要展示

2017.10.11
#### com_plan_lists
#### 迁移文件 2017_09_19_105337_create_com_plan_lists.php
- 操作：添加  字段：agent_id  作用：制作计划书的代理人id        原类型:无   目标类型：int    默认值：        原因：代理人工具我的客户中需要链表查询使用
2017.09.29
#### 表名称 com_order
#### 表说明 订单记录表
#### 迁移文件 2017_05_24_110954_create_com_order.php
- 操作：添加  字段：by_stages_way  作用：分期缴费形式  原类型:varchar   目标类型：无   默认值：无（0年表示趸交）  原因：展示订单成交时的所选的支付分期方式 目前在完成支付后才有该值

2017.10.12
#### 表名称 com_channel_operate
#### 表说明 渠道操作记录表
#### 迁移文件 2017_08_10_115838_create_com_channel
- 操作：修改  字段：issue_status
- 操作：修改  字段：issue_content

2017.10.13
#### 表名称 com_channel_operate
#### 表说明 渠道操作记录表
#### 迁移文件 2017_08_10_115838_create_com_channel
- 操作：增加  字段：init_status   初始化状态
- 操作：增加  字段：init_content   初始化备注

#### 表名称 com_channel_claim_apply
#### 表说明 渠道理赔操作表
#### 迁移文件 2017_08_10_115838_create_com_channel
- 操作：新建 字段  claim_file_id 主键');
- 操作：新建 字段  union_order_code 联合订单号
- 操作：新建 字段  channel_user_code 用户证件号
- 操作：新建 字段  warranty_code 保单号
- 操作：新建 字段  cid_files 用户身份资料
- 操作：新建 字段  bank_files 收款银行资料
- 操作：新建 字段  claim_materials 理赔资料
- 操作：新建 字段  add_push_files 补充资料
- 操作：新建 字段  claim_add_status 理赔材料状态,是否需要补充材料
- 操作：新建 字段  claim_start_time 理赔报案时间
- 操作：新建 字段  claim_start_status 理赔报案状态,是否成功

2017.10.12
#### 表 com_track、com_track_statistics
- 操作：添加表com_track、com_track_statistics  字段：xxxxxxxx   作用：埋点  原类型:    目标类型：无    默认值：     原因：埋点

2017.10.17
#### 表 com_cust_rule
- 操作：修改  字段：cust_id  修改为： user_id   作用：关联user表id  原类型: int   目标类型：int    默认值： 无    原因：逐渐取消cust表
- 操作：修改  字段：agent_id  修改为：    作用：用来给代理人分配客户  原类型: int   目标类型：int    默认值： 无    原因：给代理人分配客户

2017.10.17
#### 表 com_users
- 操作：添加  字段：occupation  修改为：    作用：存放用户的职业  原类型:    目标类型：varchar    默认值： 无    原因：代理人工具需要显示

2017.10.17
#### 表 com_order_prepare_parameter
- 操作：添加  字段：plan_id  修改为：    作用：判断订单是否通过计划书产生  原类型:    目标类型：int    默认值： 无    原因：统计计划书

2017.10.18
#### 表 com_authentication
- 操作：更改  字段：code  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：license_code  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：tax_code  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：credit_code  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：boss  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据

2017.10.18
#### 表 com_true_firm_info
- 操作：更改  字段：person_name  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：person_card_id  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：card_img_front  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：card_img_backend  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：ins_principal  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：ins_phone  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：ins_principal_code  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：license_group_id  修改为：可为空    作用：  原类型:    目标类型：int    默认值： 无    原因：代理人填写没有数据
- 操作：更改  字段：license_img  修改为：可为空    作用：  原类型:    目标类型：varchar    默认值： 无    原因：代理人填写没有数据


2017.10.18
#### 表 com_plan_recognizee
- 操作：更改  字段：cust_id  修改为：user_id    作用：关联users表  原类型: int   目标类型：int    默认值： 无    原因：取消cust表
- 操作：添加  字段：plan_policy_id  修改为：    作用：关联plan_policy表  原类型:    目标类型：int    默认值： 无    原因：关联两个表

2017.10.18
#### 表 com_plan_policy
- 操作：添加  字段：user_id  修改为：    作用：关联users表  原类型:    目标类型：int    默认值： 无    原因：计划书投保人信息与user关联
2017.10.19
#### 表名称 com_channel_insure_info
#### 表说明 渠道投保用户操作表
#### 迁移文件 2017_08_10_115838_create_com_channel
- 操作：新建 字段  channel_user_name 用户姓名
- 操作：新建 字段  channel_user_code 证件号
- 操作：新建 字段  channel_user_phone 手机号
- 操作：新建 字段  insure_start_time 起保时间
- 操作：新建 字段  insure_end_time 截至时间
- 操作：新建 字段  channel_user_sex 性别
- 操作：新建 字段  channel_user_age 年龄
- 操作：新建 字段  channel_bank_birthday 生日
- 操作：新建 字段  channel_nationality 护照国籍
- 操作：新建 字段  channel_code 渠道标识
- 操作：新建 字段  channel_code_type 证件类型

#### 表名称 com_product_down_reason
#### 表说明 产品下架原因表
#### 迁移文件 2017_05_24_100310_create_com_product
- 操作：新建 字段  id 主键ID
- 操作：新建 字段  ty_product_id 产品ID，和产品关联
- 操作：新建 字段  product_down_labels 产品选择下架标签
- 操作：新建 字段  product_down_content 产品下架原因说明

2017.10.20
#### 表名称 com_order
#### 表说明 产品下架原因表
#### 迁移文件 2017_05_24_110954_create_com_order.php
- 操作：新建 字段  ditch_id 渠道的id,为空则为用户自主购买
- 操作：新建 字段  plan_id 计划书的id,为空则为用户自主购买

#### 表名称 com_plan_lists
#### 表说明 产品下架原因表
#### 迁移文件 2017_05_24_110954_create_com_order.php
- 操作：新建 字段  read_time 计划书阅读时间
- 操作：添加状态值2 字段 status 2标识已读



2017.10.23
#### 表名称 com_labels
#### 表说明 标签表
#### 迁移文件 2017_06_23_102841_create_com_labels.php
- 操作：新建 字段  label_type   标签类型：全局标签global，特有标签special
- 操作：新建 字段  label_belong 标签归属：产品product,代理人agent，用户user

#### 表名称 com_label_relevance
#### 表说明 产品下架原因表
#### 迁移文件 2017_06_23_102841_create_com_labels.php
- 操作：新建 字段  label_type  标签类型：全局标签，特有标签，个人标签，公司标签
- 操作：新建 字段  label_belong 标签归属：产品product,代理人agent，用户user
- 操作：新建 字段  label_relevance 标签关联ID,产品ty_product_id,用户user_id,代理人agent_id
- 操作：新建 字段  label_id 标签ID

2017.10.25
#### 表名称 com_authentication_person
#### 表说明 个人、代理人认证表
#### 迁移文件 2017_08_30_141020_create_authentication_person.php
- 操作：更改 字段  code  更改为：可为空
- 操作：更改 字段  status  更改为：添加备注

2017.10.25
#### 表名称 com_true_user_info
#### 表说明 代理人认证信息表
#### 迁移文件 2017_05_09_051011_create_user_info.php
- 操作：更改 字段  card_id  更改为：可为空

#### 表名称 com_product
#### 表说明 产品表
#### 迁移文件  2017_05_24_100310_create_com_product.php
- 操作：新建 字段  sale_status 状态，在售，停售

2017.10.24
#### 表名称 com_task
#### 表说明 任务表
#### 迁移文件 2017_05_22_070620_create_com_task.php
- 操作：新建 字段  desc 描述信息
- 操作：修改 字段  type 类型：任务类型 year年类型 season季类型 month月类型

#### 表名称 com_task_ditch_agent
#### 表说明 任务_渠道_代理人关系表 用于显示任务操作记录
#### 迁移文件 2017_10_24_142009_create_com_task_ditch_agent_table.php
- 操作：新建 字段  task_id 任务ID
- 操作：新建 字段  ditch_id 渠道ID
- 操作：新建 字段  agent_id 代理人ID

#### 表名称 com_task_detail
#### 表说明 任务详细表 用于记录渠道和代理人的月任务额度
#### 迁移文件 2017_09_26_193752_create_task_detail_table.php
- 操作：删除 字段  task_id 任务ID
- 操作：删除 字段  time 时间
- 操作：新建 字段  year 年份
- 操作：新建 字段  month 月份

2017.10.25
#### 表名称 com_authentication_person
#### 表说明 个人、代理人认证表
#### 迁移文件 2017_08_30_141020_create_authentication_person.php
- 操作：更改 字段  code  更改为：可为空
- 操作：更改 字段  status  更改为：添加备注

2017.10.25
#### 表名称 com_true_user_info
#### 表说明 代理人认证信息表
#### 迁移文件 2017_05_09_051011_create_user_info.php
- 操作：更改 字段  card_id  更改为：可为空

#### 表名称 com_product
#### 表说明 产品表
#### 迁移文件  2017_05_24_100310_create_com_product.php
- 操作：新建 字段  sale_status 状态，在售，停售

2017.10.27
#### 表名称 com_plan_lists
#### 表说明 计划书表
#### 迁移文件 2017_09_19_105337_create_com_plan_lists.php
#### 操作人 
- 操作：添加 字段  send_time  类型：string 可为空

#### 表名称 com_product
#### 表说明 产品表
#### 迁移文件 2017_05_24_100310_create_com_product.php
#### 操作人 
- 操作：添加  字段：base_price  修改为：    作用：产品基础价格  原类型:    目标类型：int    默认值： 无    原因：同步产品时录入，排序、展示用
- 操作：添加  字段：base_stages_way  修改为：    作用：默认缴别  原类型:    目标类型：varchar  默认值： 无    原因：
- 操作：添加  字段：base_ratio  修改为：    作用：默认缴别对应佣金比  原类型:    目标类型：varchar  默认值： 无    原因：

2017.11.1
####表名称 com_communication
####表说明 代理人沟通记录表
#### 迁移文件 2017_11_01_160337_create_com_communication.php
#### 操作人 
- 操作：新增表

####表名称 com_evaluate
####表说明 评价/评分表
#### 迁移文件 2017_11_01_175912_create_com_evaluate.php
#### 操作人 
- 操作：新增表

2017.11.7
#### 表名称 com_channel_claim_apply
#### 表说明 渠道理赔信息表
#### 迁移文件 2017_08_10_115838_create_com_channel.php
#### 操作人 
- 操作：修改 字段  cid_files  类型：string 为 longText
- 操作：修改 字段  bank_files  类型：string 为 longText
- 操作：修改 字段  claim_materials  类型：string 为 longText
- 操作：修改 字段  add_push_files  类型：string 为 longText


2017.11.8
#### 表名称 com_product
#### 表说明 渠道理赔信息表
#### 迁移文件 2017_05_24_100310_create_com_product.php
#### 操作人 
- 操作：新增 字段  product_category  类型：string 

2017.11.9
#### 表名称 com_true_firm_info
#### 表说明 公司认证信息表
#### 迁移文件 2017_05_09_051011_create_user_info.php
#### 操作人 
- 操作：新增 字段  ins_email  类型：string 

2017.11.10
#### 表名称 com_users
#### 表说明 用户表
#### 迁移文件 2014_10_12_000000_create_users_table.php
#### 操作人 
- 操作：修改 字段  phone  操作：去掉唯一性 
- 操作：目前企业必须通过邮箱登陆，个人只能通过手机号登陆，用户区分账户和账号

2017.11.20
#### 表名称 com_comment
#### 表说明 评论/留言/交流内容表
#### 迁移文件  2017_11_20_115518_create_com_comment.php
#### 操作人 
- 操作：新增 
- 操作：系统中所有的评论/留言的内容表

2017.11.20
#### 表名称 com_liability_demand
#### 表说明 原工单管理表（新工单管理表）
#### 迁移文件  2017_05_23_094347_create_com_liability_demand.php
#### 操作人 
- 操作：修改（重构）
- 操作：工单操作记录表

2017.11.28 
### 表名称     com_timed_task
#### 表说明    定时任务管理表
#### 迁移文件  2017_11_28_com_timed_task_table.com
#### 操作人    王石磊
- 操作: 新建表
- 操作：新建 id 主键
- 操作：新建 task_name 任务名称  区分不同的定时任务
- 操作：新建 task_type 任务类型  可为空
- 操作：新建 service_ip 服务器IP  区别服务器
- 操作：新建 start_time 任务开始时间 可为空
- 操作：新建 task_time 任务执行时间  可为空
- 操作：新建 end_time 任务结束时间   可为空
- 操作：新建 timestamp 执行时间  用来判断是否超时
- 操作：新建 status 状态  判断是否执行

2017.12.1 
### 表名称     com_messages
#### 表说明    消息表
#### 迁移文件  2017_12_01_153539_create_com_messages.php
#### 操作人    陈延涛
- 操作: 新建表
- 操作：新建 id 主键
- 操作：新建 accept_id 接受者id 
- 操作：新建 content 通知内容  
- 操作：新建 timing 定时发送时间  可为空
- 操作：新建 status 消息状态 
- 操作：新建 send_time 实际发送时间  可为空
- 操作：新建 look_time 查看时间   可为空

2017.12.12
#### 表名称 com_order
#### 表说明 订单表
#### 迁移文件 2017_05_24_110954_create_com_order.php
#### 操作人  王石磊 
- 操作：新增字段
- 操作：新增 pay_account varchar  支付账户（银行卡号）

2017.12.4
### 表名称     com_message_statistics
#### 表说明    消息统计表
#### 迁移文件  2017_12_04_155609_create_com_message_statistics.php
#### 操作人    陈延涛
- 操作: 新建表
- 操作：新建 id 主键
- 操作：新建 rec_id 接受者id (关联user表id)
- 操作：新建 mes_id 消息id (关联user表id)
- 操作：新建 status 接收状态
- 操作：新建 status 接收状态

2017.12.5
### 表名称     com_setting
#### 表说明    个性化设置表
#### 迁移文件  2017_12_05_195304_create_com_setting_table.php
#### 操作人    房玉婷
- 操作: 新建表
- 操作：新建 id 主键
- 操作：新建 name 配置项名称  可为空
- 操作：新建 content 配置的具体内容(json)  可为空
- 操作：新建 type 类型(1:配置项,2:布局)

2017.12.8
### 表名称     com_liability_demand
#### 表说明    工单管理
#### 迁移文件  2017_05_23_094347_create_com_liability_demand.php
#### 操作人    常莹莹
- 操作：新建 close_status 关闭状态  可为空
- 操作：新建 reason 关闭原因

2018.1.22
### 表名称     com_group_ins_cust
#### 表说明    移动端团险投保加人
#### 迁移文件  2018_01_22_164727_create_com_group_ins_cust.php
#### 操作人    陈延涛
- 操作：新建表，表结构为被保人的信息
