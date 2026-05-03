<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_administrator = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin1234'),
        ]);

        $super_administrator->syncRoles(['super_administrator']);

        $customer = User::create([
            'name' => 'customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('customer1234'),
        ]);

        $customer->syncRoles(['customer']);

        User::factory()->count(10)->create();
    }
}
