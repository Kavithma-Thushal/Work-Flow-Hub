<?php

namespace Database\Seeders;

use App\Models\LeavePolicy;
use App\Models\LeaveType;
use App\Models\PolicyHasType;
use Illuminate\Database\Seeder;

class PolicyHasTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffPolicy = LeavePolicy::where('name', 'Staff')->first();
        $managerPolicy = LeavePolicy::where('name', 'Manager')->first();

        $casualLeaveType = LeaveType::where('name', 'Casual')->first();
        $annualLeaveType = LeaveType::where('name', 'Annual')->first();

        $policyLeaves = [
            [
                'leave_policy_id' => $staffPolicy->id,
                'leave_type_id' => $casualLeaveType->id,
                'amount' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'leave_policy_id' => $staffPolicy->id,
                'leave_type_id' => $annualLeaveType->id,
                'amount' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'leave_policy_id' => $managerPolicy->id,
                'leave_type_id' => $casualLeaveType->id,
                'amount' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'leave_policy_id' => $managerPolicy->id,
                'leave_type_id' => $annualLeaveType->id,
                'amount' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($policyLeaves as $policyLeave) {
            PolicyHasType::updateOrCreate(
                [
                    'leave_policy_id' => $policyLeave['leave_policy_id'],
                    'leave_type_id' => $policyLeave['leave_type_id'],
                ],
                [
                    'amount' => $policyLeave['amount'],
                    'created_at' => $policyLeave['created_at'],
                    'updated_at' => $policyLeave['updated_at'],
                ]
            );
        }
    }
}
