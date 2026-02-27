<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('majors')->insert(
            [
                'major_code' => 'RPL',
                'major_name' => 'Rekayasa Perangkat Lunak'
            ],
            [
                'major_code' => 'TKJ',
                'major_name' => 'Teknik Komputer dan Jaringan'
            ],
            [
                'major_code' => 'SK',
                'major_name' => 'Seni Karawitan'
            ]
        );
    }
}
