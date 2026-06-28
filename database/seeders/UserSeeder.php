<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $owner   = Role::firstOrCreate(['name' => 'owner']);
        $kasir   = Role::firstOrCreate(['name' => 'kasir']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);

        // Users
        User::firstOrCreate(
            ['email' => 'owner@armalcellular.test'], 
            ['username' => 'Owner Armal',   'password' => Hash::make('password'), 'role_id' => $owner->id,   'contact' => '081234567890', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'kasir@armalcellular.test'], 
            ['username' => 'Kasir Armal',   'password' => Hash::make('password'), 'role_id' => $kasir->id,   'contact' => '081234567891', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'teknisi@armalcellular.test'], 
            ['username' => 'Teknisi Armal', 'password' => Hash::make('password'), 'role_id' => $teknisi->id, 'contact' => '081234567892', 'status' => 'active']
        );
    }
}
