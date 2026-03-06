<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Major;
use App\Models\SchoolYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = Major::all();
        $schoolYears = SchoolYear::all();

        Classes::factory(20)
            ->recycle($schoolYears)
            ->recycle($majors)
            ->create();
    }
}
