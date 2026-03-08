<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $majors   = Major::all();

        Book::factory(20)
            ->recycle($subjects)
            ->recycle($majors)
            ->afterCreating(function (Book $book) {
                $book->load('subject', 'major');
                $book->generateBookCode();
                // Yg ingin ditambahkan: ngubah format kode buku, jadi bentuk awal kode buku adalah [ID-[KODE MAPEL]-[TINGKAT]-[KODE JURUSAN]-[SEMSTER]
                // Contohnya: 01-MAT-11-RPL-2,
                // ingin diubah jadi [ID KESELURUHAN BUKU]-[KODE MAPEL]-[TINGKAT]-[KODE JURUSAN]-[SEMESTER]-[ID KHUSUS BUKU DENGAN JENIS YG SAMA]
                // Contohnya: 02-IPA-12-TKJ-1-19
            })
            ->create();
    }
}
