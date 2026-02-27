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
                $subjectCode = $book->subject->subject_code;
                $majorCode   = $book->major->major_code;
                $semester    = $book->semester === 'odd' ? '1' : '2';

                $book->update([
                    'book_code' => strtoupper($subjectCode)
                        . '-' . $book->id
                        . '-' . $book->grade
                        . '-' . strtoupper($majorCode)
                        . '-' . $semester,
                ]);
            })
            ->create();
    }
}
