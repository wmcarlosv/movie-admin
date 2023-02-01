<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	[
        		'name' => 'administrador',
        		'email' => 'administrador@gmail.com',
        		'role' => 'admin',
        		'password' => bcrypt('123456')
        	]
        ]);
    }
}
