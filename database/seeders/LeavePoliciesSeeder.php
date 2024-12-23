<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeavePoliciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('leave_policies')->insert([
            [
                'name' => 'Default Policy',
                'casual_leaves' => 12,
                'annual_leaves' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Policy',
                'casual_leaves' => 15,
                'annual_leaves' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
