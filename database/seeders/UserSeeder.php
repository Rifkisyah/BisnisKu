<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if (!$store) return;

        $kasirRole  = Role::where('name', 'kasir')->first();
        $teknisiRole = Role::where('name', 'teknisi')->first();

        // Tambahan staf UMKM mikro konter realistis (sift sore / asisten teknisi)
        if ($kasirRole) {
            User::firstOrCreate(
                ['email' => 'kasir2@armalcellular.test'],
                [
                    'username' => 'Kasir Sore Armal',
                    'password' => Hash::make('password'),
                    'role_id'  => $kasirRole->id,
                    'store_id' => $store->id,
                    'contact'  => '081234567894',
                    'status'   => 'active',
                ]
            );
        }

        if ($teknisiRole) {
            User::firstOrCreate(
                ['email' => 'teknisi2@armalcellular.test'],
                [
                    'username' => 'Asisten Teknisi Armal',
                    'password' => Hash::make('password'),
                    'role_id'  => $teknisiRole->id,
                    'store_id' => $store->id,
                    'contact'  => '081234567895',
                    'status'   => 'active',
                ]
            );
        }
    }
}
