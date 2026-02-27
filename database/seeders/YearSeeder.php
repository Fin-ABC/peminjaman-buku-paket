<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('school_years')->insert(
            ['year_name' => '2024/2025'],
            ['year_name' => '2025/2026', 'is_active' => true],
            ['year_name' => '2026/2027'],
        );
    }
}
