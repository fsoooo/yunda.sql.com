<?php
namespace App\Console\Commands;


use App\Helper\TimeStamp;
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

class AddBank extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'addBank';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'addBank Command description';

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
		$bank_common = OldBank::select('id','cust_id','bank','bank_code','bank_city','phone');
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
				Redis::rpush('bank_info', json_encode($value));
			}
		}
		for ($i = 1; $i <= 100; $i++) {
			$bank_info = Redis::rpop('bank_info');
			$add_res = $this->doAddBank(json_decode($bank_info, true));
			dump($add_res);
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
		$person_data = OnlinePersonRefer::where('out_person_id', $bank_info['cust_id'])
			->select('account_uuid', 'manager_uuid')
			->first();
		$bank_data = [];
		$bank_data['account_uuid'] = $person_data['account_uuid']??"0";
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
				$authorize_data['account_uuid'] = $person_data['account_uuid']??"0";
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
					return '数据插入失败';
				}
			} catch (\Exception $e) {
				DB::rollBack();
				return 'sql执行失败';
			}
		}else{
			return 'bank not empty';
		}
	}

}