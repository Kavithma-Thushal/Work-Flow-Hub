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
                'casual_leaves' => 10,
                'annual_leaves' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
