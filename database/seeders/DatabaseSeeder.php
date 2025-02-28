<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create([
            'id'        => 3,
            'role'      => 'superadmin',
            'deskripsi' => 'admin divisi bertugas melakukan permintaan '
        ]);

        User::create([
            'id'        => 3,
            'name'      => 'chandratz',
            'username'     => 'superadmin',
            'password'  =>  bcrypt('1234'),
            'role_id'   =>  3
        ]);
    }
}
