<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //需要被执行的填充类
        // $this->call(UsersTableSeeder::class);
        $this->call(AdminUsersTableSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(SmsModelSeeder::class);
        $this->call(AgentSeeder::class);
        $this->call(LabelGroupSeeder::class);
        $this->call(TimedTaskSeeder::class);
    }
}
