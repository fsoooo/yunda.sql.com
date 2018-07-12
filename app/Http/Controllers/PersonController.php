<?php
namespace App\Http\Controllers;

use App\Helper\TimeStamp;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\Person;
use App\Models\OnlineAccount;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class PersonController
{


	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

    public function index()
    {
    	if(!Redis::exists('person_max_id')&&!Redis::exists('person_data')){
			$person = Person::limit(10000)->get();
			$max_id = $person[count($person)-1]['id'];//把最大的id存在redis里
			Redis::set('person_max_id',$max_id);
			Redis::set('person_data',$person);
		}else{
			$person = Redis::get('person_data');
			$max_id = Redis::get('person_max_id');
		}
		if(!is_array($person)){
			$person = json_decode($person,true);
		}
		if(!Redis::exists('person_info')||Redis::lLen('person_info')==0){
    		if(!empty($person)){
				foreach ($person as $value){
					Redis::rpush('person_info',json_encode($value));
				}
			}
		}
		$count =  Redis::lLen('person_info');
		if($count<=0){
			$person = Person::limit($max_id+1,10000)->get();
			$max_id = $person[count($person)-1]['id'];//把最大的id存在redis里
			Redis::set('person_max_id',$max_id);
			Redis::set('person_data',$person);
		}
		if(!empty($person)&&$count==0){
			foreach ($person as $value){
				Redis::rpush('person_info',json_encode($value));
			}
		}
		for($i=1;$i<=100;$i++){
			$person_info = Redis::rpop('person_info');
			$this->addData(json_decode($person_info,true));
		}
		echo $count;
    }

	public function addData($data){
		$insert_data = [];
		$insert_data['name'] = $data['name'];
		$insert_data['head'] = $data['head'];
		$insert_data['nickname'] = '';
		$insert_data['cert_type'] = $data['papers_type'];
		$insert_data['cert_code'] = $data['papers_code'];
		$insert_data['cert_start'] = $data['papers_start'];
		$insert_data['cert_end'] = $data['papers_end'];
		$insert_data['authentication'] = $data['authentication']??"1";
		$insert_data['sex'] = $data['sex']??"1";
		$insert_data['birthday'] = $data['birthday'];
		$insert_data['address'] = $data['address'];
		$insert_data['address_detail'] = $data['address_detail'];
		$insert_data['phone'] = $data['phone'];
		$insert_data['email'] = $data['email'];
		$insert_data['front_key'] = $data['up_url'];
		$insert_data['back_key'] = $data['down_url'];
		$insert_data['handheld_key'] = $data['person_url'];
		$insert_data['state'] = '1';//0删除
		$insert_data['created_at'] = $this->date;
		$insert_data['updated_at'] = $this->date;
		$repeat_res = OnlinePerson::where('cert_code',$insert_data['cert_code'])->select('id')->first();
		if(empty($repeat_res)){
			DB::beginTransaction();
			try{
				$person_id = OnlinePerson::insertGetId($insert_data);
				$account_uuid = 154000000+$data['id'];
				if($person_id>0){
					$insert_data_account = [];
					$insert_data_account['account_uuid'] =$account_uuid.'';
					$insert_data_account['username'] = '';
					$insert_data_account['password'] = '';
					$insert_data_account['phone'] = $insert_data['phone']??'';
					$insert_data_account['email'] = $insert_data['email']??'';
					$insert_data_account['token'] = '';
					$insert_data_account['status'] = '1';
					$insert_data_account['sys_id'] = '1000';
					$insert_data_account['user_type'] = '1';
					$insert_data_account['user_id'] = $person_id??'0';
					$insert_data_account['salt'] = $this->getSalt();
					$insert_data_account['state'] = '1';//0删除
					$insert_data_account['origin'] = 'YUNDA';//0删除
					$insert_data_account['created_at'] = $this->date;
					$insert_data_account['updated_at'] = $this->date;
					$repeat_res = OnlineAccount::where('account_uuid',$insert_data_account['account_uuid'])->select('id')->first();
					if(empty($repeat_res)){
						$addres = OnlineAccount::insertGetId($insert_data_account);
						if($addres>0){
							$insert_data_personrefer =[];
							$insert_data_personrefer['account_uuid'] = $account_uuid.'';
							$insert_data_personrefer['manager_uuid'] = '14463303497682968';
							$insert_data_personrefer['out_person_id'] = $data['id'];
							$insert_data_personrefer['person_id'] = $person_id;
							$insert_data_personrefer['created_at'] = $this->date;
							$insert_data_personrefer['updated_at'] = $this->date;
							$repeat_res = OnlinePersonRefer::where('account_uuid',$insert_data_personrefer['account_uuid'])->select('id')->first();
							if(empty($repeat_res)){
								$add = OnlinePersonRefer::insertGetId($insert_data_personrefer);
								if($add>0){
									DB::commit();
									return '成功';
								}else{
									DB::rollBack();
									return '失败';
								}
							}
						}else{
							DB::rollBack();
							return '失败';
						}
					}
				}else{
					DB::rollBack();
					return '失败';
				}
			}catch (\Exception $e){
				DB::rollBack();
				return '失败';
			}
		}
	}

	private function getSalt()
	{
		$str = substr(md5(time()), 0, 6);
		return $str;
	}


}