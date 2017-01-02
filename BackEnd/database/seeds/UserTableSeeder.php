<?php
use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table("users")->delete();
        for($i=0; $i < 10; $i++){
            User::create([
                'name' => 'username',
                'email' => 'account'.$i,
                'password' => 'password'.$i,


            ]);
        }
    }
}
