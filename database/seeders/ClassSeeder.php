<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = Major::all();
        Classes::factory(10)->recycle($majors)->create();
    }
}
