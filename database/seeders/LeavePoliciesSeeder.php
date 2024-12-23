<?php

namespace Database\Seeders;

use App\Models\LeavePolicy;
use Illuminate\Database\Seeder;

class LeavePoliciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'name' => 'Staff',
                'casual_leaves' => 10,
                'annual_leaves' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'casual_leaves' => 20,
                'annual_leaves' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($policies as $policy) {
            LeavePolicy::updateOrCreate(
                ['name' => $policy['name']],
                [
                    'casual_leaves' => $policy['casual_leaves'],
                    'annual_leaves' => $policy['annual_leaves'],
                    'created_at' => $policy['created_at'],
                    'updated_at' => $policy['updated_at'],
                ]
            );
        }
    }
}
