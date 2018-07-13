<?php
namespace App\Http\Controllers;

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

class ClaimController
{

	public function __construct()
	{
		$this->date = TimeStamp::getMillisecond();
	}

	public function Index(){

	}
}