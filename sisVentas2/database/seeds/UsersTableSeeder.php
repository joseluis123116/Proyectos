<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
                //insertando desde codigo 
        DB::table('users')->insert([
        	[
        	"name"=>"jose",
        	"email"=>"jose.munoz@plus.pe",
        	"password"=> bcrypt('123456Aa'),
        	]

        	]);
    }
}
