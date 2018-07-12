<?php
/**
 * Created by PhpStorm.
 * User: wangsl
 * Date: 2017/12/1
 * Time: 15:07
 * 韵达投保定时任务（凌晨地点到十点）
 */
namespace App\Console\Commands;

use App\Helper\LogHelper;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Console\Command;
use App\Helper\IdentityCardHelp;
use Illuminate\Http\Request;
use App\Helper\DoChannelsSignHelp;
use App\Helper\RsaSignHelp;
use App\Helper\AesEncrypt;
use Validator, DB, Image, Schema;
use App\Models\Channel;
use App\Models\UserChannel;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Session,Cache;
use App\Models\Order;
use App\Models\OrderParameter;
use App\Models\WarrantyPolicy;
use App\Models\WarrantyRecognizee;
use App\Models\WarrantyRule;
use \Illuminate\Support\Facades\Redis;
use App\Models\ChannelPrepareInfo;
use App\Models\ChannelOperate;
use App\Models\TimedTask;



class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test Command description';
	
	/**
     * test the crond service 
     *
     * @var string
     */
    public function handle()
	{
       $log = "定时任务开始了".date('Y_m_d H_m_s')."\n";
       LogHelper::logChannelSuccess($log, 'test_crond');
	}

}
