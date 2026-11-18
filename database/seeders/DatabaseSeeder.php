<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Reuse existing departments; create only missing ones.
            $departmentNames = [
                'Information Technology',
                'Sales',
                'Human Resources',
            ];

            foreach ($departmentNames as $name) {
                Department::firstOrCreate(
                    ['name' => $name],
                    [
                        'description' => $name . ' department',
                        'status' => 'active',
                    ]
                );
            }

            // Ensure a default admin exists without changing an existing account.
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin',
                    'username' => 'admin-' . Str::lower(Str::random(8)),
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'admin',
                    'status' => 'active',
                ]
            );

            // Reuse the Laravel skill if it already exists.
            Skill::firstOrCreate(
                ['name' => 'Laravel'],
                [
                    'description' => 'Laravel web application development',
                    'status' => 'active',
                ]
            );

            // Create 50 sample employees only if the sample set is absent.
            $sampleCount = Employee::where('employee_code', 'like', 'DEMO-%')
                ->count();

            if ($sampleCount === 0) {
                Employee::factory()
                    ->count(50)
                    ->create([
                        'department_id' => Department::query()
                            ->inRandomOrder()
                            ->value('id'),
                    ]);
            }
        });
    }
}
