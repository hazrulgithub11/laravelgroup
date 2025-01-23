<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::create([
            'user_id' => 1, // Make sure this user exists
            'provider_id' => 1, // Make sure this provider exists
            'total' => 99.99,
            'status' => 'pending'
        ]);
    }
}
