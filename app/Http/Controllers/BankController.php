<?php
namespace App\Http\Controllers;

use App\Helper\TimeStamp;
use App\Models\OnlinePersonRefer;
use App\Models\OnlinePerson;
use App\Models\Person;
use App\Models\OnlineAccount;
use Illuminate\Support\Facades\Redis;
use DB;

class BankController
{


	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

    public function index()
    {
    	if(!Redis::exists('max_id')&&!Redis::exists('person_data')){
			$person = Person::limit(10000)->get();
			$max_id = $person[count($person)-1]['id'];//把最大的id存在redis里
			Redis::set('max_id',$max_id);
			Redis::set('person_data',$person);
		}else{
			$person = Redis::get('person_data');
			$max_id = Redis::get('max_id');
		}
		$count =  Redis::lLen('person_info');
		if($count<=0){
			$person = Person::limit($max_id,10000)->get();
			$max_id = $person[count($person)-1]['id'];//把最大的id存在redis里
			Redis::set('max_id',$max_id);
			Redis::set('person_data',$person);
		}
		if(!is_array($person)){
			$person = json_decode($person,true);
		}
		$this->doAdd($person);
    }


}