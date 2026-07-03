<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);
        $gudang = Role::firstOrCreate(['name' => 'gudang']);

        User::firstOrCreate(
            ['email' => 'owner@armalcellular.test'], 
            ['username' => 'Owner Armal', 'password' => Hash::make('password'), 'role_id' => $owner->id, 'contact' => '081234567890', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'kasir@armalcellular.test'], 
            ['username' => 'Kasir Armal', 'password' => Hash::make('password'), 'role_id' => $kasir->id, 'contact' => '081234567891', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'teknisi@armalcellular.test'], 
            ['username' => 'Teknisi Armal', 'password' => Hash::make('password'), 'role_id' => $teknisi->id, 'contact' => '081234567892', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'gudang@armalcellular.test'], 
            ['username' => 'Gudang Armal', 'password' => Hash::make('password'), 'role_id' => $gudang->id, 'contact' => '081234567893', 'status' => 'active']
        );

        $faker = Faker::create('id_ID');
        for ($i = 0; $i < 17; $i++) {
            User::firstOrCreate(
                ['email' => $faker->unique()->safeEmail()],
                [
                    'username' => $faker->name(),
                    'password' => Hash::make('password'),
                    'role_id' => $faker->randomElement([$kasir->id, $teknisi->id]),
                    'contact' => $faker->phoneNumber(),
                    'status' => 'active',
                ]
            );
        }
    }
}
