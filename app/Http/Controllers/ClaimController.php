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

class ClaimController
{

	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

	public function Index(){

	}
}