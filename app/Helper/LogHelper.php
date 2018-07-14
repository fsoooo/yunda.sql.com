<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LogHelper
{
	//日志
	public static function logs($data, $from = null, $type = null, $file_name = 'laravel_logs')
	{
		$log = "[ " . $file_name . " ] [" . $from . '] [' . $type . "] [" . Carbon::now() . "] \n" . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
		$date = date('Y_m_d');
		$file_path = storage_path('logs/' . $file_name . '_' . $date . '.log');
		file_put_contents($file_path, $log, FILE_APPEND);
	}
}









