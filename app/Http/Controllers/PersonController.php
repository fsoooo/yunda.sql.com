<?php
namespace App\Http\Controllers;

use App\Helper\TimeStamp;
use App\Helper\LogHelper;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\OldPerson;
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
		$this->log = new LogHelper();
	}

    public function index()
    {
		set_time_limit(0);
    	$person_common = OldPerson::select('id','name', 'papers_type', 'papers_code', 'papers_start', 'papers_end', 'sex', 'birthday', 'address', 'address_detail', 'phone', 'email', 'postcode', 'cust_type', 'authentication', 'up_url', 'down_url', 'person_url', 'head', 'company_id', 'del', 'status');
    	if(!Redis::exists('person_max_id')&&!Redis::exists('person_data')){
			$person = $person_common->limit(1000)->get();
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
		if(!empty($person)&&Redis::lLen('person_info')==0){
			foreach ($person as $value){
				Redis::rpush('person_info',json_encode($value));
			}
		}
		for($i=1;$i<=100;$i++){
			$person_info = Redis::rpop('person_info');
			$addRes = $this->addData(json_decode($person_info,true));
		}
		if(Redis::lLen('person_info')<1){
			$person =  $person_common->limit($max_id+1,1000)->get();
			$max_id = $person[count($person)-1]['id'];//把最大的id存在redis里
			Redis::set('person_max_id',$max_id);
			Redis::set('person_data',$person);
		}
		echo 'max_id_'.$max_id.'<br/>';
		echo 'person_info_count_'.Redis::lLen('person_info');
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
									LogHelper::logs('插入成功','addPerson','','add_person_success');
									return '成功';
								}else{
									DB::rollBack();
									LogHelper::logs('插入失败','addPerson','','add_person_error');
									return '失败';
								}
							}else{
								LogHelper::logs('person_refer not empty','addPerson','','add_person_error');
								return 'person_refer not empty';
							}
						}else{
							DB::rollBack();
							LogHelper::logs('插入失败','addPerson','','add_person_error');
							return '失败';
						}
					}else{
						LogHelper::logs('account not empty','addPerson','','add_person_error');
						return 'account not empty';
					}
				}else{
					DB::rollBack();
					LogHelper::logs('插入失败','addPerson','','add_person_error');
					return '失败';
				}
			}catch (\Exception $e){
				DB::rollBack();
				LogHelper::logs('插入失败','addPerson','','add_person_error');
				return '失败';
			}
		}else{
			LogHelper::logs('person not empty','addPerson','','add_person_error');
			return 'person not empty';
		}
	}

	private function getSalt()
	{
		$str = substr(md5(time()), 0, 6);
		return $str;
	}


}