<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table("admin")->delete();
        for($i=0; $i < 1; $i++){
            User::create([
                'name' => 'username',
                'email' => 'account'.$i,
                'password' => 'password'.$i,


            ]);
        }
    }
}
