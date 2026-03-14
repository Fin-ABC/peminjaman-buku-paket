<?php

namespace App\Http\Controllers;

use App\Models\BookItem;
use App\Models\Classes;
use App\Models\Major;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\matches;

class ReturnController extends Controller
{
    private array $gradeMap = [
        'X'   => '10',
        'XI'  => '11',
        'XII' => '12',
    ];

    public function step1()
    {
        return view('return.step1');
    }

    public function step2(Request $request)
    {
        $level = $request->query('level');

        if (!array_key_exists($level, $this->gradeMap)) {
            return redirect()->route('return.step1');
        }

        $majors = Major::orderBy('major_name')->get();

        return view('return.step2', compact('level', 'majors'));
    }

    public function step3(Request $request)
    {
        $level   = $request->query('level');
        $majorId = $request->query('major_id');

        if (!array_key_exists($level, $this->gradeMap) || !$majorId) {
            return redirect()->route('return.step1');
        }

        $major   = Major::findOrFail($majorId);
        $grade   = $this->gradeMap[$level];

        $classes = Classes::where('major_id', $majorId)
            ->where('grade', $grade)
            ->orderBy('class_name')
            ->get();

        return view('return.step3', compact('level', 'major', 'classes'));
    }

    public function step4(Request $request)
    {
        $level   = $request->query('level');
        $majorId = $request->query('major_id');
        $classId = $request->query('class_id');

        if (!array_key_exists($level, $this->gradeMap) || !$majorId || !$classId) {
            return redirect()->route('return.step1');
        }

        $class = Classes::findOrFail($classId);

        return view('return.step4', compact('level', 'majorId', 'classId', 'class'));
    }

    public function step5(Request $request)
    {
        $level    = $request->query('level');
        $majorId  = $request->query('major_id');
        $classId  = $request->query('class_id');
        $semester = $request->query('semester');

        if (!array_key_exists($level, $this->gradeMap) || !$majorId || !$classId || !in_array($semester, ['odd', 'even'])) {
            return redirect()->route('return.step1');
        }

        $major = Major::findOrFail($majorId);
        $class = Classes::findOrFail($classId);

        return view('return.step5', compact('level', 'majorId', 'classId', 'semester', 'major', 'class'));
    }

    public function verifyStep5(Request $request)
    {
        $request->validate([
            'nis_1'    => ['required', 'digits_between:10,16'],
            'nis_2'    => ['required', 'digits_between:10,16', 'different:nis_1'],
            'level'    => ['required'],
            'major_id' => ['required'],
            'class_id' => ['required'],
            'semester' => ['required', 'in:odd,even'],
        ], [
            'nis_2.different'      => 'Kedua NIS tidak boleh sama.',
            'nis_1.digits_between' => 'NIS harus 10-16 digit angka.',
            'nis_2.digits_between' => 'NIS harus 10-16 digit angka.',
        ]);

        $classId = $request->input('class_id');
        $nis1    = $request->input('nis_1');
        $nis2    = $request->input('nis_2');

        $validCount = Student::where('class_id', $classId)
            ->whereIn('nis', [$nis1, $nis2])
            ->count();

        if ($validCount < 2) {
            return back()
                ->withInput()
                ->with('verification_error', 'Salah satu NIS tidak ditemukan di kelas ini.');
        }

        return redirect()->route('return.step6', [
            'level'    => $request->input('level'),
            'major_id' => $request->input('major_id'),
            'class_id' => $classId,
            'semester' => $request->input('semester'),
        ]);
    }

    public function step6(Request $request)
    {
        $level    = $request->query('level');
        $majorId  = $request->query('major_id');
        $classId  = $request->query('class_id');
        $semester = $request->query('semester');

        if (!array_key_exists($level, $this->gradeMap) || !$majorId || !$classId || !in_array($semester, ['odd', 'even'])) {
            return redirect()->route('return.step1');
        }

        $grade = $this->gradeMap[$level];
        $major = Major::findOrFail($majorId);
        $class = Classes::findOrFail($classId);

        // Ambil semua transaksi untuk kelas ini
        $transactions = Transaction::where('class_id', $classId)
            ->where('semester', $semester)
            ->with('book')
            ->get();

        // Bangun data buku — hitung sudah kembali & belum kembali per buku
        $books = $transactions->map(function ($transaction) {
            $returned = TransactionDetail::where('transaction_id', $transaction->id)
                ->whereIn('status', ['Returned'])
                ->count();

            $notReturned = TransactionDetail::where('transaction_id', $transaction->id)
                ->whereNotIn('status', ['Returned'])
                ->count();

            return [
                'transaction_id' => $transaction->id,
                'book_id'        => $transaction->book->id,
                'book_code'      => $transaction->book->book_code,
                'title'          => $transaction->book->title,
                'returned'       => $returned,
                'not_returned'   => $notReturned,
            ];
        })
            // Urutkan: belum kembali terbanyak di kiri
            ->sortByDesc('not_returned')
            ->values();

        return view('return.step6', compact(
            'level',
            'majorId',
            'classId',
            'semester',
            'major',
            'class',
            'books'
        ));
    }

    public function loadStudents(Request $request)
    {
        $transactionId = $request->query('transaction_id');

        if (!$transactionId) {
            return response()->json([]);
        }

        $details = TransactionDetail::where('transaction_id', $transactionId)
            ->with('student')
            ->get()
            ->map(function ($detail) {
                return [
                    'detail_id'    => $detail->id,
                    'student_id'   => $detail->student->id,
                    'student_name' => $detail->student->student_name,
                    'nis'          => $detail->student->nis,
                    'status'       => $detail->status,
                    'is_overdue'   => $detail->status === 'Overdue',
                ];
            });

        return response()->json($details);
    }

    public function confirmReturn(Request $request)
    {
        $request->validate([
            'transaction_id'         => ['required', 'exists:transactions,id'],
            'details'                => ['required', 'array', 'min:1'],
            'details.*.detail_id'    => ['required', 'exists:transaction_details,id'],
            'details.*.status'       => ['required', 'in:Borrowed,Returned,lost,Overdue'],
        ]);

        $transactionId = $request->input('transaction_id');
        $details       = $request->input('details');
        $classId       = $request->input('class_id');

        // dd($request);


        DB::transaction(function () use ($transactionId, $details) {
            $conditionMap = [
                'Borrowed' => 'borrowed',
                'Returned' => 'good',
                'Overdue' => 'borrowed',
                'lost' => 'lost'
            ];

            foreach ($details as $item) {
                TransactionDetail::where('id', $item['detail_id'])
                    ->update(['status' => $item['status']]);

                $book = TransactionDetail::where('id', $item['detail_id'])->first();
                $condition = $conditionMap[$item['status']];

                BookItem::where('id', $book->id)->update(['condition' => $condition]);
            }

            // Cek apakah semua sudah dikembalikan / lost
            $stillBorrowed = TransactionDetail::where('transaction_id', $transactionId)
                ->whereNotIn('status', ['Returned', 'lost'])
                ->count();

            Transaction::where('id', $transactionId)
                ->update(['is_all_returned' => $stillBorrowed === 0]);

        });

        // Hitung jumlah yang dikembalikan untuk success page
        $returnedCount = collect($details)
            ->where('status', 'Returned')
            ->count();

        $transaction = Transaction::with('book')->find($transactionId);

        return redirect()->route('return.success')->with([
            'book_title'     => $transaction->book->title,
            'student_count'  => $returnedCount,
            'return_date'    => now()->translatedFormat('d F Y'),
        ]);
    }
}
