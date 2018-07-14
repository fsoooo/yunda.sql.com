<?php
namespace App\Console\Commands;


use App\Helper\TimeStamp;
use App\Helper\LogHelper;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\OldPerson;
use App\Models\OnlineAccount;
use App\Models\OnlineBankAuthorize;
use App\Models\OnlineBank;
use App\Models\OldContractInfo;
use App\Models\OldChannelInsureSeting;
use App\Models\OldBank;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class AddBankAuthorize extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'addBankAuthorize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'addBankAuthorize Command description';

	/**
	 * Create a new command instance.
	 * @return void
	 * 初始化
	 *
	 */
	public function __construct(Request $request)
	{
		parent::__construct();
		set_time_limit(0);//永不超时
		$this->date = TimeStamp::getMillisecond();
	}

	public function handle()
	{
		set_time_limit(0);
		$contract_common = OldContractInfo::select('id','request_serial','contract_expired_time','contract_id','change_type','contract_code','openid','channel_user_code',DB::raw('`created_at` AS `create`'),DB::raw('`updated_at` AS `update`'));
		if (!Redis::exists('contract_max_id') && !Redis::exists('contract_data')) {
			$contract_data = $contract_common->limit(10000)->get();
			if(!empty($contract_data)){
				$max_id = $contract_data[count($contract_data)-1]['id'];//把最大的id存在redis里
				Redis::set('contract_max_id', $max_id);
				Redis::set('contract_data', json_encode($contract_data));
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
				Redis::rpush('contract_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 1000; $i++) {
			$contract_info = Redis::rpop('contract_info');
			$add_res = $this->doAddBankAuthorize(json_decode($contract_info, true));
			dump($add_res);
		}
		$count = Redis::lLen('contract_info');
		if ($count <= 0) {
			//->where('id','>',$max_id)->limit(10000)->get();
			$contract_data = $contract_common->where('id','>',$max_id)->limit(10000)->get();
			if(count($contract_data)<10000){
				$max_id = 0;//重置
			}else{
				$max_id = $contract_data[count($contract_data) - 1]['id'];//把最大的id存在redis里
			}
			Redis::set('contract_max_id', $max_id);
			Redis::set('contract_data', $contract_data);
		}
		echo $count;
	}

	public function doAddBankAuthorize($contract_info){
		$person_data = OnlinePerson::where('cert_code', $contract_info['channel_user_code'])
			->with('personRefer')
			->select('id')
			->first();
		if(empty($person_data)){
			LogHelper::logs('no account_uuid','addbank','','add_bank_authorize_error');
			return 'no account_uuid';
		}else if(isset($person_data['personRefer'])&&empty($person_data['personRefer'])){
			LogHelper::logs('no account_uuid','addbank','','add_bank_authorize_error');
			return 'no account_uuid';
		}
		$authorize_data = [];
		$authorize_data['account_uuid'] = $person_data['personRefer']['account_uuid'];
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
		if(!$authorize_data['account_uuid']||$authorize_data['account_uuid']=='0'||empty($authorize_data['account_uuid'])){
			//return 'no account_uuid';
		}
		$authorize_data['account_uuid'] = '15400013912';
		$repeat_res = OnlineBankAuthorize::where('account_uuid',$authorize_data['account_uuid'])
			->select('id')
			->first();
		if(empty($repeat_res)){
			OnlineBankAuthorize::insertGetId($authorize_data);
			LogHelper::logs('插入成功','addBankAuthorize','','add_bankAuthorize_success');
			return '插入结果';
		}else{
			DB::connection('online_mysql')->update('update 
							`bank_authorize` 
						set 
							`request_serial` = '.'"'.$authorize_data['request_serial'].'"'.' 
						and `contract_expired_time`= '.'"'.$authorize_data['contract_expired_time'].'"'.' 
						and `contract_id`= '.'"'.$authorize_data['contract_id'].'"'.' 
						and `change_type`= '.'"'.$authorize_data['change_type'].'"'.' 
						and `contract_code`= '.'"'.$authorize_data['contract_code'].'"'.' 
						and `openid`= '.'"'.$authorize_data['openid'].'"'.' 
						and `updated_at`='.'"'.$this->date.'"'.' 
					  where `account_uuid` = '.'"'.$authorize_data['account_uuid'].'"');
//			 OnlineBankAuthorize::where('account_uuid',$authorize_data['account_uuid'])
//				->update(
//					[
//					'request_serial' => $authorize_data['request_serial'],
//					'contract_expired_time' => $authorize_data['contract_expired_time'],
//					'contract_id' => $authorize_data['contract_id'],
//					'change_type' => $authorize_data['change_type'],
//					'contract_code' => $authorize_data['contract_code'],
//					'openid' => $authorize_data['openid']
//				]);
			LogHelper::logs('更新成功','updateBankAuthorize','','add_bankAuthorize_success');
			return '更新结果';
		}
	}

}