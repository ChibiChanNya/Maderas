<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'chibi',
            'full_name' => 'André Cantón',
            'password' => bcrypt('password'),
            'last_login' => Carbon::now(),
            'device_ip' => '172.19.0.1',
            'description' => 'Super User',
            'permissions' => '11111',
            'email' => 'chibi@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
