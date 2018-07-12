<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\YunDaPrepare::class,
        Commands\Msg::class,
        Commands\YundaCallback::class,
        Commands\Test::class,
        Commands\YunDaPay::class,
        Commands\YunDaIssue::class,
        Commands\YunDaPre::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('yunda_prepare')->everyMinute()->between('00:00', '10:00')->runInBackground();
        $schedule->command('yunda_pay')->everyMinute()->between('00:00', '23:59')->runInBackground();
        $schedule->command('yunda_issue')->everyMinute()->between('00:00', '23:59')->runInBackground();
        $schedule->command('yunda_pre')->everyMinute()->between('00:00', '23:59')->runInBackground();
        // $schedule->command('yundacallback')->everyMinute()->between('21:30', '23:59');//晚上下班之后
        //$schedule->command('yunda')->dailyAt('23:59');//指定时间执行
        //$schedule->command('baidu')->everyMinute();//每分钟
        //$schedule->command('baidu_work_clear')->everyMinute()->runInBackground(); //并行执行
        //$schedule->command('reminders:send')->hourly()->between('7:00', '22:00');//在指定时间内每小时执行一次
        //$schedule->command('reminders:send')->hourly()->unlessBetween('23:00', '4:00');//在指定时间外每小时执行一次
        //$schedule->command('emails:send')->withoutOverlapping();//避免任务重叠
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }

}

//todo 调度常用选项
//->cron('* * * * *');	在自定义Cron调度上运行任务
//->everyMinute();	每分钟运行一次任务
//->everyFiveMinutes();	每五分钟运行一次任务
//->everyTenMinutes();	每十分钟运行一次任务
//->everyThirtyMinutes();	每三十分钟运行一次任务
//->hourly();	每小时运行一次任务
//->daily();	每天凌晨零点运行任务
//->dailyAt('13:00');	每天13:00运行任务
//->twiceDaily(1, 13);	每天1:00 & 13:00运行任务
//->weekly();	每周运行一次任务
//->monthly();	每月运行一次任务
//->monthlyOn(4, '15:00');	每月4号15:00运行一次任务
//->quarterly();	每个季度运行一次
//->yearly();	每年运行一次
//->timezone('America/New_York');	设置时区
