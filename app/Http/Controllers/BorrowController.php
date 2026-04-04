<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Classes;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    private array $gradeMap = [
        'X'   => '10',
        'XI'  => '11',
        'XII' => '12',
        '10'   => '10',
        '11'  => '11',
        '12' => '12',
    ];

    // Step 1 — Pilih Tingkat
    public function step1()
    {
        return view('borrow.step1');
    }

    // Step 2 — Pilih Jurusan
    public function step2(Request $request)
    {
        $grade = $request->query('grade');

        // Validasi: grade harus ada dan valid
        if (!in_array($grade, $this->gradeMap)) {
            return redirect()->route('borrow.step1');
        }

        $majors = Major::whereNot('major_code', 'UM')
            ->get();

        return view('borrow.step2', compact('grade', 'majors'));
    }

    // Step 3 — Pilih Kelas
    public function step3(Request $request)
    {
        $grade = $request->query('grade');
        $majorId = $request->query('major_id');

        if (!in_array($grade, $this->gradeMap) || !$majorId) {
            return redirect()->route('borrow.step1');
        }

        $major = Major::findOrFail($majorId);

        $classes = Classes::where('major_id', $majorId)
            ->where('grade', $grade)
            ->whereNot('grade', 'lulus')
            ->orderBy('major_id')
            ->get();

        // dd([
        //     'major_id' => $majorId,
        //     'grade'    => $grade,
        //     'classes_count' => $classes->count(),
        //     // Cek tanpa filter grade, apakah ada data untuk major ini?
        //     'tanpa_grade' => Classes::where('major_id', $majorId)->get()->toArray(),
        //     // Cek semua kelas yang ada di DB
        //     'semua_kelas' => Classes::take(5)->get(['id', 'grade', 'major_id', 'year_id', 'class_name'])->toArray(),
        // ]);
        return view('borrow.step3', compact('grade', 'major', 'classes'));
    }

    public function step4(Request $request)
    {
        $grade   = $request->query('grade');
        $majorId = $request->query('major_id');
        $classId = $request->query('class_id');

        if (!in_array($grade, $this->gradeMap) || !$majorId || !$classId) {
            return redirect()->route('borrow.step1');
        }

        $class = Classes::findOrFail($classId);

        return view('borrow.step4', compact('grade', 'majorId', 'classId', 'class'));
    }

    public function step5(Request $request)
    {
        $grade    = $request->query('grade');
        $majorId  = $request->query('major_id');
        $classId  = $request->query('class_id');
        $semester = $request->query('semester');

        if (!in_array($grade, $this->gradeMap) || !$majorId || !$classId || !in_array($semester, ['odd', 'even'])) {
            dd($grade);
            // dd($majorId);
            dd($classId);
            dd($semester);
            return redirect()->route('borrow.step1');
        }

        $major = Major::findOrFail($majorId);
        $class = Classes::findOrFail($classId);

        return view('borrow.step5', compact('grade', 'majorId', 'classId', 'semester', 'major', 'class'));
    }

    public function verifyStep5(Request $request)
    {
        $request->validate([
            'nisn_1'    => ['required', 'digits_between:10,16'],
            'nisn_2'    => ['required', 'digits_between:10,16', 'different:nisn_1'],
            'grade'    => ['required'],
            'major_id' => ['required'],
            'class_id' => ['required'],
            'semester' => ['required', 'in:odd,even'],
        ], [
            'nisn_2.different'        => 'Kedua NISN tidak boleh sama.',
            'nisn_1.digits_between'   => 'NISN harus 10 digit angka.',
            'nisn_2.digits_between'   => 'NISN harus 10 digit angka.',
        ]);

        $classId = $request->input('class_id');
        $nisn1    = $request->input('nisn_1');
        $nisn2    = $request->input('nisn_2');

        // Cek apakah kedua NISN ada di kelas yang dipilih
        $validCount = \App\Models\Student::where('class_id', $classId)
            ->whereIn('nisn', [$nisn1, $nisn2])
            ->where('status', 'active')
            ->count();

        if ($validCount < 2) {
            return back()
                ->withInput()
                ->with('verification_error', 'Salah satu NISN tidak ditemukan di kelas ini.');
        }

        // Lanjut ke Step 6
        return redirect()->route('borrow.step6', [
            'grade'    => $request->input('grade'),
            'major_id' => $request->input('major_id'),
            'class_id' => $classId,
            'semester' => $request->input('semester'),
        ]);
    }

    public function step6(Request $request)
    {
        $grade    = $request->query('grade');
        $majorId  = $request->query('major_id');
        $classId  = $request->query('class_id');
        $semester = $request->query('semester');

        if (
            !in_array($grade, $this->gradeMap) || !$majorId || !$classId || !in_array($semester, ['odd', 'even'])
        ) {
            dd('gagal');
            return redirect()->route('borrow.step1');
        }

        // $grade = $this->gradeMap[$grade];
        $major = Major::findOrFail($majorId);
        $class = Classes::findOrFail($classId);

        // Ambil buku yang sesuai grade, jurusan yang dipilih + jurusan umum (Um)
        $umMajor = Major::where('major_code', 'Um')->first();
        $umMajorId = $umMajor?->id;

        $majorIds = array_filter([$majorId, $umMajorId]);

        $books = Book::whereIn('major_id', $majorIds)
            ->where('grade', $grade)
            ->where('semester', $semester)
            ->get();

        // Ambil semua siswa dari kelas, beserta status sudah meminjam per buku
        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('student_name')
            ->get();

        // Ambil student_id yang sudah meminjam tiap buku (status bukan Returned/lost)
        $borrowedMap = [];
        foreach ($books as $book) {
            $borrowedStudentIds = TransactionDetail::whereHas('transaction', function ($q) use ($book) {
                $q->where('book_id', $book->id);
            })
                ->whereNotIn('status', ['Returned', 'lost'])
                ->pluck('student_id')
                ->toArray();

            $borrowedMap[$book->id] = $borrowedStudentIds;
        }

        return view('borrow.step6', compact(
            'grade',
            'majorId',
            'classId',
            'semester',
            'major',
            'class',
            'books',
            'students',
            'borrowedMap'
        ));
    }

    public function confirm(Request $request)
    {
        // ============ VALIDASI ============
        $request->validate([
            'book_id'       => ['required', 'exists:books,id'],
            'student_ids'   => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
            'class_id'      => ['required', 'exists:classes,id'],
            'semester'      => ['required', 'in:odd,even'],
        ]);

        $bookId     = $request->input('book_id');
        $studentIds = $request->input('student_ids');
        $classId    = $request->input('class_id');
        $semester   = $request->input('semester');

        // ============ AMBIL DATA ============
        $book       = Book::findOrFail($bookId);
        $schoolYear = SchoolYear::where('is_active', true)->first();

        if (!$schoolYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif. Hubungi admin.');
        }

        $today      = now();
        $returnDate = $today->copy()->addMonths(6); // 6 bulan dari sekarang

        // ============ CARI BOOK_ITEMS YANG TERSEDIA ============
        $availableItemsQuery = BookItem::where('book_id', $bookId)
            ->whereIn('condition', ['good', 'damaged']) // Exclude 'lost'
            ->orderBy('id', 'asc');                      // Urut ID terkecil

        $availableItems = $availableItemsQuery->get();

        // ============ CEK STOK CUKUP ============
        if ($availableItems->count() < count($studentIds)) {
            return back()->with('error', "Stok buku tidak cukup! Tersedia: {$availableItems->count()}, Dibutuhkan: " . count($studentIds));
        }

        // ============ PROSES PEMINJAMAN ============
        DB::transaction(function () use (
            $book,
            $bookId,
            $classId,
            $semester,
            $schoolYear,
            $studentIds,
            $availableItems,
            $today,
            $returnDate
        ) {
            // 1. Buat atau ambil Transaction (per kelas)
            $transaction = Transaction::firstOrCreate(
                [
                    'book_id'  => $bookId,
                    'class_id' => $classId,
                    'year_id'  => $schoolYear->id,
                    'semester' => $semester,
                ],
                [
                    'transaction_date' => $today->toDateString(),
                    'is_all_returned'  => false,
                ]
            );

            // 2. Assign book_item ke setiap siswa
            foreach ($studentIds as $index => $studentId) {
                $bookItem = $availableItems->get($index);

                if (!$bookItem) {
                    throw new \Exception("Book item tidak tersedia untuk siswa index {$index}");
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'book_item_id'   => $bookItem->id,
                    'student_id'     => $studentId,
                    'status'         => 'Borrowed',
                    'return_date'    => $returnDate->toDateString(),
                    'note'           => null,
                ]);

                BookItem::where('id', $bookItem->id)->update(['condition' => 'borrowed']);
            }

            // 3. Update remaining_stock di tabel books
            // Hitung jumlah buku 'good' yang dipinjam
            $goodCount = $availableItems->take(count($studentIds))
                ->where('condition', 'good')
                ->count();

            $book->decrement('remaining_stock', $goodCount);
        });

        // ============ REDIRECT KE SUCCESS ============
        return redirect()->route('borrow.success')->with([
            'book_title'    => $book->title,
            'class_name'    => Classes::find($classId)->class_name,
            'student_count' => count($studentIds),
            'borrow_date' => $today,
        ]);
    }
}
