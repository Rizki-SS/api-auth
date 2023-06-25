<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
        	'name' => 'Isyak Rizqi', 
        	'email' => 'admin@gmail.com',
        	'password' => bcrypt('123456')
        ], ["email" => "admin@gmail.com"]);

        $role = Role::findByName('Admin', 'api');
        $user->assignRole("Admin");
    }
}
