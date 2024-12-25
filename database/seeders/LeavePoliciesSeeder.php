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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($policies as $policy) {
            LeavePolicy::updateOrCreate(
                ['name' => $policy['name']],
                [
                    'created_at' => $policy['created_at'],
                    'updated_at' => $policy['updated_at'],
                ]
            );
        }
    }
}
