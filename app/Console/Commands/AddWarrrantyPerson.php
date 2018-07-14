<?php
namespace App\Console\Commands;

use App\Helper\TimeStamp;
use App\Helper\LogHelper;
use App\Models\OnlineCustWarrantyCost;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\OldPerson;
use App\Models\OnlineAccount;
use App\Models\OnlineCustWarrantyPerson;
use App\Models\OnlineCustWarranty;
use App\Models\OldCustWarrantyPerson;
use App\Models\OldCustWarranty;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class AddWarrrantyPerson extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'addWarrrantyPerson';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'addWarrrantyPerson Command description';

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
		$warranty_person_commom = OldCustWarrantyPerson::select('id','warranty_uuid','out_order_no','type', 'relation_name', 'name', 'card_type', 'card_code', 'phone', 'occupation', 'birthday','sex', 'age','email', 'nationality', 'annual_income', 'height', 'weight', 'area', 'address', 'start_time', 'end_time');
		if (!Redis::exists('warranty_person_max_id') && !Redis::exists('warranty_person_data')) {
			$warranty_person_data = $warranty_person_commom->limit(10000)->get();
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
		for ($i = 1; $i <= 1000; $i++) {
			$warranty_person_info = Redis::rpop('warranty_person_info');
			$add_res = $this->addWarrantyPerson(json_decode($warranty_person_info, true));
			dump($add_res);
		}
		$count = Redis::lLen('warranty_person_info');
		if ($count <= 0) {
			$warranty_person_data = $warranty_person_commom->limit($warranty_person_max_id+1, 10000)->get();
			$warranty_person_max_id = $warranty_person_data[count($warranty_person_data) - 1]['id'];//把最大的id存在redis里
			Redis::set('warranty_person_max_id', $warranty_person_max_id);
			Redis::set('warranty_person_data', $warranty_person_data);
		}
		echo $count;
	}

	public function addWarrantyPerson($warranty_person_data)
	{
		$insert_warranty_person = [];
		$insert_warranty_person['warranty_uuid'] = $warranty_person_data['warranty_uuid']??'0';//不为空
		$insert_warranty_person['type'] = $warranty_person_data['type']??"1";//人员类型: 1投保人 2被保人 3受益人
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
		$repeat_res = OnlineCustWarrantyPerson::where('warranty_uuid', $insert_warranty_person['warranty_uuid'])
			->where('type',$insert_warranty_person['type'])
			->select('id')
			->first();
		if (empty($repeat_res)) {
			OnlineCustWarrantyPerson::insertGetId($insert_warranty_person);
			LogHelper::logs('成功','addwarrantyperson','','add_warranty_person_success');
			return '成功';
		}else{
			LogHelper::logs('warranty_person not empty','addwarrantyperson','','add_warranty_person_error');
			return 'warranty_person not empty';
		}
	}

}