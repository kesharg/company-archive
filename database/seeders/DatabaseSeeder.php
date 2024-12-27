<?php

namespace Database\Seeders;

use App\Models\DistrackModel;
use App\Models\Distributor;
use App\Models\Package;
use App\Models\Tenant;
use App\Models\User;
use Database\Factories\PackageFactory;
use Database\Factories\TenantFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        User::create([
            "first_name" => "Super Admin",
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin24'),

            "email_verified_at" => now()
        ]);


        // User::factory(10)->create();

        // Package::factory(4)->create();
    }
}
