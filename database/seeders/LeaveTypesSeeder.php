<?php

namespace Database\Seeders;

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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annual',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($types as $type) {
            LeaveType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'created_at' => $type['created_at'],
                    'updated_at' => $type['updated_at'],
                ]
            );
        }
    }
}
