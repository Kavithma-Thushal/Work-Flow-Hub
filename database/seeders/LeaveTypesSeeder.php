<?php

namespace Database\Seeders;

use App\Models\LeavePolicy;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Casual',
                'amount' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annual',
                'amount' => '20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($types as $type) {
            LeaveType::updateOrCreate(
                ['name' => $type['name']],
                ['amount' => $type['amount']],
                [
                    'created_at' => $type['created_at'],
                    'updated_at' => $type['updated_at'],
                ]
            );
        }
    }
}
