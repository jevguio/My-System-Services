<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'name' => 'Basic Subscription',
                'description' => 'Access to basic features',
                'price' => 9.99,
                'stripe_price_id' => 'price_1BasicExample',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro Subscription',
                'description' => 'Access to all features',
                'price' => 19.99,
                'stripe_price_id' => 'price_1ProExample',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise Subscription',
                'description' => 'Customized enterprise features',
                'price' => 49.99,
                'stripe_price_id' => 'price_1EnterpriseExample',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
