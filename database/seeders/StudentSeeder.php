<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes    = Classes::all();
        $schoolYears = SchoolYear::all();

        Student::factory(34)
            ->state(['status' => 'active'])
            ->recycle($classes)
            ->create();

        Student::factory(34)
            ->state(['status' => 'graduated'])
            ->recycle($classes)
            ->create();
    }
}
