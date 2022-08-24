<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->command->info('Admin user upload successfully');
        $this->call(UserSeeder::class);
        $this->command->info('Fake users upload successfully');
        $this->call(CustomerRequestSeeder::class);
        $this->command->info('Fake customer request uploaded !');
    }
}
