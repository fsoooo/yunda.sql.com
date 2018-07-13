<?php
namespace App\Console\Commands;

class Requset
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'request';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'request Command description';

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

	}

	public function handle()
	{

	}

}