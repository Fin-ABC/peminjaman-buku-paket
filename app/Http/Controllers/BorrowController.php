<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Major;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    private array $gradeMap = ['10', '11', '12'];

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

        $majors = Major::orderBy('major_name')->get();

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
            ->orderBy('major_id')
            ->get();

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

        return view('borrow.step4', compact('grade', 'majorId', 'class'));
    }
}
