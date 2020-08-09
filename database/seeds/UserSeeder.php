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
        		'email' => 'cvargas@frontuari.net',
        		'role' => 'admin',
        		'password' => bcrypt('Car2244los*')
        	]
        ]);
    }
}
