<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();


        DB::table('users')->insert([
            'sub' => 'auth0|5b83d39cf107661378def796',
            'name' => 'caleboki@laravue.com',
            'nickname' => 'caleboki',

        ]);
    }
}
