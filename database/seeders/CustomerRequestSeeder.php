<?php

namespace Database\Seeders;

use App\Models\CustomerRequest;
use Illuminate\Database\Seeder;

class CustomerRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerRequest::factory(25)->create();
    }
}
