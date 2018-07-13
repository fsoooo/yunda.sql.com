<?php
namespace App\Http\Controllers;

use App\Helper\TimeStamp;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\Person;
use App\Models\OnlineAccount;
use App\Models\OnlineBankAuthorize;
use App\Models\OnlineBank;
use App\Models\ContractInfo;
use App\Models\ChannelInsureSeting;
use App\Models\Bank;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class BankController
{

	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

	public function bankIndex(){
		set_time_limit(0);
		$bank_common = Bank::select('id','cust_id','bank','bank_code','bank_city','phone');
		if (!Redis::exists('bank_max_id') && !Redis::exists('bank_data')) {
			$bank_data = $bank_common->limit(1000)->get();
			if(!empty($bank_data)){
				$max_id = $bank_data[count($bank_data) - 1]['id'];//把最大的id存在redis里
				Redis::set('bank_max_id', $max_id);
				Redis::set('bank_data', json_encode($bank_data));
			}else{
				return 1;
			}
		} else {
			$bank_data = Redis::get('bank_data');
			$max_id = Redis::get('bank_max_id');
		}
		if (!is_array($bank_data)) {
			$bank_data = json_decode($bank_data, true);
		}
		if (!Redis::exists('bank_info') || Redis::lLen('bank_info') == 0) {
			if(!empty($bank_data)) {
				foreach ($bank_data as $value) {
					Redis::rpush('bank_info', json_encode($value));
				}
			}
		}
		$count = Redis::lLen('bank_info');
		if (!empty($bank_data) && $count == 0) {
			foreach ($bank_data as $value) {
				$person_data = OnlinePersonRefer::where('out_person_id', $value['cust_id'])
					->select('account_uuid', 'manager_uuid')
					->first();
				if (!empty($person_data)) {
					$value['account_uuid'] = $person_data['account_uuid'];
					$value['manager_uuid'] = $person_data['manager_uuid'];
				}
				Redis::rpush('bank_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 100; $i++) {
			$bank_info = Redis::rpop('bank_info');
			$add_res = $this->doAddBank(json_decode($bank_info, true));
			echo $add_res;
		}
		$count = Redis::lLen('bank_info');
		if ($count <= 0) {
			$bank_data = $bank_common->limit($max_id+1,1000)->get();
			$max_id = $bank_data[count($bank_data) - 1]['id'];//把最大的id存在redis里
			Redis::set('bank_max_id', $max_id);
			Redis::set('bank_data', $bank_data);
		}
		echo $count;
	}

	public function doAddBank($bank_info){
		$bank_data = [];
		$bank_data['account_uuid'] = $bank_info['account_uuid']??"";
		$bank_data['bank_name'] = $bank_info['bank'];
		$bank_data['bank_city'] = $bank_info['bank_city'];
		$bank_data['bank_code'] = $bank_info['bank_code'];
		$bank_data['bank_type'] = '';
		$bank_data['phone'] = $bank_info['phone'];
		$bank_data['status'] = '1';
		$bank_data['state'] = '1';
		$bank_data['created_at'] = $this->date;
		$bank_data['updated_at'] = $this->date;
		$repeat_res = OnlineBank::where('bank_code',$bank_data['bank_code'])
			->select('id')
			->first();
		if(empty($repeat_res)){
			DB::beginTransaction();
			try {
				$bank_id = OnlineBank::insertGetId($bank_data);
				$authorize_data = [];
				$authorize_data['account_uuid'] = $bank_info['account_uuid'];
				$authorize_data['bank_id'] = $bank_id;
				$authorize_data['request_serial'] = '';
				$authorize_data['contract_expired_time'] = '';
				$authorize_data['contract_id'] = '';
				$authorize_data['change_type'] = '';
				$authorize_data['contract_code'] = '';
				$authorize_data['openid'] = '';
				$authorize_data['state'] = '1';
				$authorize_data['created_at'] = $this->date;
				$authorize_data['updated_at'] = $this->date;
				$repeat_res = OnlineBankAuthorize::where('account_uuid',$authorize_data['account_uuid'])
					->where('bank_id',$authorize_data['bank_id'])
					->select('id')->first();
				if(empty($repeat_res)){
					$bank_authorize_id = OnlineBankAuthorize::insertGetId($authorize_data);
				}else{
					return 'bank_authorize not empty';
				}
				if ($bank_id && $bank_authorize_id) {
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
			return 'bank not empty';
		}
	}

	public function bankAuthorizeIndex(){
		set_time_limit(0);
		$contract_common = ContractInfo::select('id','request_serial','contract_expired_time','contract_id','change_type','contract_code','openid','channel_user_code');
		if (!Redis::exists('contract_max_id') && !Redis::exists('contract_data')) {
			$contract_data = $contract_common->limit(1000)->get();
			if(!empty($contract_data)){
				$max_id = $contract_data[count($contract_data)-1]['id'];//把最大的id存在redis里
				Redis::set('contract_max_id', $max_id);
				Redis::set('contract_data', json_encode($contract_data));
			}else{
				return 1;
			}
		} else {
			$contract_data = Redis::get('contract_data');
			$max_id = Redis::get('contract_max_id');
		}
		if (!is_array($contract_data)) {
			$contract_data = json_decode($contract_data, true);
		}
		if (!Redis::exists('contract_info') || Redis::lLen('contract_info') == 0) {
			if(!empty($bank_data)) {
				foreach ($bank_data as $value) {
					Redis::rpush('contract_info', json_encode($value));
				}
			}
		}
		$count = Redis::lLen('contract_info');
		if (!empty($contract_data) && $count == 0) {
			foreach ($contract_data as $value) {
				$person_data = OnlinePerson::where('cert_code', $value['channel_user_code'])
					->select('account_uuid', 'manager_uuid')
					->first();
				if (!empty($person_data)) {
					$value['account_uuid'] = $person_data['account_uuid'];
					$value['manager_uuid'] = $person_data['manager_uuid'];
				}
				Redis::rpush('contract_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 100; $i++) {
			$contract_info = Redis::rpop('contract_info');
			$add_res = $this->doAddBankAuthorize(json_decode($contract_info, true));
			echo $add_res;
		}
		$count = Redis::lLen('contract_info');
		if ($count <= 0) {
			$contract_data = $contract_common->limit($max_id+1,1000)->get();
			$max_id = $contract_data[count($contract_data) - 1]['id'];//把最大的id存在redis里
			Redis::set('contract_max_id', $max_id);
			Redis::set('contract_data', $contract_data);
		}
		echo $count;
	}

	public function doAddBankAuthorize($contract_info){
		$authorize_data = [];
		$authorize_data['account_uuid'] = $contract_info['account_uuid']??"";
		$authorize_data['bank_id'] = '';
		$authorize_data['request_serial'] =  $contract_info['request_serial'];
		$authorize_data['contract_expired_time'] =  $contract_info['contract_expired_time'];
		$authorize_data['contract_id'] =  $contract_info['contract_id'];
		$authorize_data['change_type'] =  $contract_info['change_type'];
		$authorize_data['contract_code'] =  $contract_info['contract_code'];
		$authorize_data['openid'] =  $contract_info['openid'];
		$authorize_data['state'] = '1';
		$authorize_data['created_at'] = $this->date;
		$authorize_data['updated_at'] = $this->date;
		$repeat_res = OnlineBankAuthorize::where('account_uuid',$authorize_data['account_uuid'])
			->select('id')->first();
		if(empty($repeat_res)){
			$add_bank_authorize = OnlineBankAuthorize::insertGetId($authorize_data);
		}else{
			$add_bank_authorize = OnlineBankAuthorize::where('account_uuid',$authorize_data['account_uuid'])
				->update([
					'request_serial' =>  $contract_info['request_serial'],
					'contract_expired_time' =>  $contract_info['contract_expired_time'],
					'contract_id' =>  $contract_info['contract_id'],
					'change_type' =>  $contract_info['change_type'],
					'contract_code' =>  $contract_info['contract_code'],
					'openid' =>  $contract_info['openid'],
					'updated_at' =>  $this->date
				]);
		}
	}

}