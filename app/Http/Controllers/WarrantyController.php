<?php

namespace App\Http\Controllers;

use App\Helper\TimeStamp;
use App\Models\OnlineCustWarrantyCost;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\Person;
use App\Models\OnlineAccount;
use App\Models\OnlineCustWarrantyPerson;
use App\Models\OnlineCustWarranty;
use App\Models\CustWarrantyPerson;
use App\Models\CustWarranty;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class WarrantyController
{

	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

	public function warrantyIndex()
	{
		set_time_limit(0);
		$warranty_common = CustWarranty::select('id','warranty_uuid','pro_policy_no','warranty_code','business_no','comb_product','comb_warranty_code','company_id','user_id','user_type','agent_id','ditch_id','plan_id','product_id','start_time','end_time','ins_company_id','count','pay_time','pay_count','pay_way','by_stages_way','is_settlement','warranty_url','warranty_from','type','check_status','pay_status','warranty_status','resp_insure_msg','resp_pay_msg','state');
		if (!Redis::exists('warranty_max_id') && !Redis::exists('warranty_data')) {
			$warranty = $warranty_common->limit(100)->get();
			if(!empty($warranty)){
				$max_id = $warranty[count($warranty) - 1]['id'];//把最大的id存在redis里
				Redis::set('warranty_max_id', $max_id);
				Redis::set('warranty_data', json_encode($warranty));
			}else{
				return 1;
			}
		} else {
			$warranty = Redis::get('warranty_data');
			$max_id = Redis::get('warranty_max_id');
		}
		if (!is_array($warranty)) {
			$warranty = json_decode($warranty, true);
		}
		if (!Redis::exists('warranty_info') || Redis::lLen('warranty_info') == 0) {
			if(!empty($warranty)) {
				foreach ($warranty as $value) {
					Redis::rpush('warranty_info', json_encode($value));
				}
			}
		}
		$count = Redis::lLen('warranty_info');
		if (!empty($warranty) && $count == 0) {
			foreach ($warranty as $value) {
				$person_data = OnlinePersonRefer::where('out_person_id', $value['user_id'])
					->select('account_uuid', 'manager_uuid')
					->first();
				if (!empty($person_data)) {
					$value['account_uuid'] = $person_data['account_uuid'];
					$value['manager_uuid'] = $person_data['manager_uuid'];
				}
				Redis::rpush('warranty_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 10; $i++) {
			$warranty_info = Redis::rpop('warranty_info');
			$add_res = $this->addWarranty(json_decode($warranty_info, true));
			echo $add_res;
		}
		$count = Redis::lLen('warranty_info');
		if ($count <= 0) {
			$warranty = $warranty_common->limit($max_id+1,100)->get();
			$max_id = $warranty[count($warranty) - 1]['id'];//把最大的id存在redis里
			Redis::set('warranty_max_id', $max_id);
			Redis::set('warranty_data', $warranty);
		}
		echo 'max_id_'.$max_id;
		echo 'warranty_info_'.$warranty;
	}

	public function addWarranty($warranty_data)
	{
		$insert_warranty = [];
		$insert_warranty['warranty_uuid'] = $warranty_data['warranty_uuid'];//不为空
		$insert_warranty['pre_policy_no'] = $warranty_data['pro_policy_no'] ?? '';
		$insert_warranty['warranty_code'] = $warranty_data['warranty_code'] ?? '';
		$insert_warranty['comb_product'] = $warranty_data['comb_product'] ?? '0';//'组合产品  0 不是  1是',
		$insert_warranty['comb_warranty_code'] = $warranty_data['comb_warranty_code'] ?? '';//组合保单号
		$insert_warranty['business_no'] = $warranty_data['business_no'] ?? '';//业务识别号

		$insert_warranty['manager_uuid'] = $warranty_data['manager_uuid'] ?? '';
		$insert_warranty['account_uuid'] = $warranty_data['account_uuid'] ?? '';

		$insert_warranty['agent_id'] = $warranty_data['agent_id'] ?? '';
		$insert_warranty['channel_id'] = $warranty_data['ditch_id'] ?? '0';
		$insert_warranty['plan_id'] = $warranty_data['plan_id'] ?? '0';
		$insert_warranty['product_id'] = $warranty_data['product_id'] ?? '0';
		$insert_warranty['start_time'] = $warranty_data['start_time'] ?? '';
		$insert_warranty['end_time'] = $warranty_data['end_time'] ?? '';
		$insert_warranty['ins_company_id'] = $warranty_data['company_id'] ?? '0';
		$insert_warranty['count'] = '1';//购买份数
		$insert_warranty['pay_category_id'] = '';//缴别ID
		$insert_warranty['integral'] = '';//积分
		$insert_warranty['express_no'] = '';//快递单号
		$insert_warranty['express_company_name'] = '';//快递公司名称
		$insert_warranty['express_address'] = '';//邮寄详细地址
		$insert_warranty['express_province_code'] = '';//省
		$insert_warranty['express_city_code'] = '';//市
		$insert_warranty['express_county_code'] = '';//地区
		$insert_warranty['express_email'] = '';//邮箱
		$insert_warranty['delivery_type'] = '';//快递方式，0-自取，1-快递',
		$insert_warranty['order_time'] = '';//保单下单时间
		$insert_warranty['is_settlement'] = $warranty_data['is_settlement'] ?? '0';//佣金 0表示未结算，1表示已结算
		$insert_warranty['warranty_url'] = $warranty_data['warranty_url'] ?? '';
		$insert_warranty['warranty_from'] = $warranty_data['warranty_from'] ?? '';//不为空,保单来源 1 自购 2线上成交 3线下成交 4导入
		$insert_warranty['type'] = $warranty_data['type'] ?? '0';
		$insert_warranty['warranty_status'] = $warranty_data['warranty_status'] ?? '';
		$insert_warranty['resp_code'] = '';//投保回执CODE
		$insert_warranty['resp_msg'] = $warranty_data['resp_insure_msg'] ?? $warranty_data['resp_pay_msg'];//投保回执信息
		$insert_warranty['state'] = $warranty_data['state'] ?? '';//删除标识 0删除 1可用
		$insert_warranty['created_at'] = $this->date;
		$insert_warranty['updated_at'] = $this->date;
		$repeat_res = OnlineCustWarranty::where('warranty_uuid', $warranty_data['warranty_uuid'])->select('id')->first();
		if (empty($repeat_res)) {
			DB::beginTransaction();
			try {
				$warranty_id = OnlineCustWarranty::insertGetId($insert_warranty);
				$insert_warranty_cost = [];
				$insert_warranty_cost['warranty_uuid'] = $warranty_data['warranty_uuid'];//不为空
				$insert_warranty_cost['pay_time'] = $warranty_data['pay_time'];//应支付时间
				$insert_warranty_cost['phase'] = '1';//分期：第几期
				$insert_warranty_cost['premium'] = '0';//保单价格
				$insert_warranty_cost['tax_money'] = '0';//税费
				$insert_warranty_cost['actual_pay_time'] = $warranty_data['pay_time'];//实际支付时间
				$insert_warranty_cost['pay_way'] = '1';//支付方式 1 银联 2 支付宝 3 微信 4现金
				$insert_warranty_cost['pay_money'] = '0';
				$insert_warranty_cost['pay_status'] = '0';//支付状态
				$insert_warranty_cost['is_settlement'] = '0';//结算状态 0-未结算，1-已结算'
				$insert_warranty_cost['bill_uuid'] = '';//结算单uuid
				$insert_warranty_cost['created_at'] = $this->date;
				$insert_warranty_cost['updated_at'] = $this->date;
				$repeat_res = OnlineCustWarrantyCost::where('warranty_uuid', $warranty_data['warranty_uuid'])->select('id')->first();
				if (empty($repeat_res)) {
					$warranty_cost_id = OnlineCustWarrantyCost::insertGetId($insert_warranty_cost);
				} else {
					$warranty_cost_id = 0;
				}
				if ($warranty_id && $warranty_cost_id) {
					DB::commit();
					return '成功';
				} else {
					DB::rollBack();
					return '失败';
				}
			} catch (\Exception $e) {
				DB::rollBack();
				return '失败';
			}
		}else{
			return 'warranty not empty';
		}
	}

	public function warrantyPersonIndex()
	{
		set_time_limit(0);
		$warranty_person_commom = CustWarrantyPerson::select('id','warranty_uuid','out_order_no','type', 'relation_name', 'name', 'card_type', 'card_code', 'phone', 'occupation', 'birthday','sex', 'age','email', 'nationality', 'annual_income', 'height', 'weight', 'area', 'address', 'start_time', 'end_time');
		if (!Redis::exists('warranty_person_max_id') && !Redis::exists('warranty_person_data')) {
			$warranty_person_data = $warranty_person_commom->limit(20)->get();
			$warranty_person_max_id = $warranty_person_data[count($warranty_person_data) - 1]['id'];//把最大的id存在redis里
			Redis::set('warranty_person_max_id', $warranty_person_max_id);
			Redis::set('warranty_person_data', $warranty_person_data);
		} else {
			$warranty_person_data = Redis::get('warranty_person_data');
			$warranty_person_max_id = Redis::get('warranty_person_max_id');
		}
		if (!is_array($warranty_person_data)) {
			$warranty_person_data = json_decode($warranty_person_data, true);
		}
		if (!Redis::exists('warranty_person_info') || Redis::lLen('warranty_person_info') == 0) {
			if(!empty($warranty_person_data)){
				foreach ($warranty_person_data as $value) {
					Redis::rpush('warranty_person_info', json_encode($value));
				}
			}
		}
		$count = Redis::lLen('warranty_person_info');
		if (!empty($warranty_person_data) && $count == 0) {
			foreach ($warranty_person_data as $value) {
				Redis::rpush('warranty_person_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 10; $i++) {
			$warranty_person_info = Redis::rpop('warranty_person_info');
			$add_res = $this->addWarrantyPerson(json_decode($warranty_person_info, true));
			echo $add_res;
		}
		$count = Redis::lLen('warranty_person_info');
		if ($count <= 0) {
			$warranty_person_data = $warranty_person_commom->limit($warranty_person_max_id+1, 20)->get();
			$warranty_person_max_id = $warranty_person_data[count($warranty_person_data) - 1]['id'];//把最大的id存在redis里
			Redis::set('warranty_person_max_id', $warranty_person_max_id);
			Redis::set('warranty_person_data', $warranty_person_data);
		}
		echo $count;
	}

	public function addWarrantyPerson($warranty_person_data)
	{
		$insert_warranty_person = [];
		$insert_warranty_person['warranty_uuid'] = $warranty_person_data['warranty_uuid'];//不为空
		$insert_warranty_person['type'] = $warranty_person_data['type'];//人员类型: 1投保人 2被保人 3受益人
		$insert_warranty_person['relation_name'] = $warranty_person_data['relation_name'];
		$insert_warranty_person['out_order_no'] = $warranty_person_data['out_order_no'];
		$insert_warranty_person['name'] = $warranty_person_data['name'];
		$insert_warranty_person['card_type'] = $warranty_person_data['card_type'] ?? '1';
		$insert_warranty_person['card_code'] = $warranty_person_data['card_code'];
		$insert_warranty_person['phone'] = $warranty_person_data['phone'];
		$insert_warranty_person['occupation'] = $warranty_person_data['occupation'];
		$insert_warranty_person['birthday'] = $warranty_person_data['birthday'];
		$insert_warranty_person['sex'] = $warranty_person_data['sex'] ?? '1';
		$insert_warranty_person['age'] = $warranty_person_data['age'];
		$insert_warranty_person['email'] = $warranty_person_data['email'];
		$insert_warranty_person['nationality'] = $warranty_person_data['nationality'];
		$insert_warranty_person['annual_income'] = $warranty_person_data['annual_income'];
		$insert_warranty_person['height'] = $warranty_person_data['height'];
		$insert_warranty_person['weight'] = $warranty_person_data['weight'];
		$insert_warranty_person['area'] = $warranty_person_data['area'];
		$insert_warranty_person['address'] = $warranty_person_data['address'];
		$insert_warranty_person['start_time'] = $data['start_time'] ?? "0";
		$insert_warranty_person['end_time'] = $data['end_time'] ?? "0";
		$insert_warranty_person['record_start_time'] = '0';
		$insert_warranty_person['record_end_time'] = '0';
		$repeat_res = OnlineCustWarrantyPerson::where('warranty_uuid', $warranty_person_data['warranty_uuid'])
			->where('type',$warranty_person_data['type'])
			->select('id')
			->first();
		if (empty($repeat_res)) {
			OnlineCustWarrantyPerson::insertGetId($insert_warranty_person);
		}else{
			return 'warranty_person not empty';
		}
	}

}