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
            'nis_1'    => ['required', 'digits_between:10,16'],
            'nis_2'    => ['required', 'digits_between:10,16', 'different:nis_1'],
            'grade'    => ['required'],
            'major_id' => ['required'],
            'class_id' => ['required'],
            'semester' => ['required', 'in:odd,even'],
        ], [
            'nis_2.different'        => 'Kedua NIS tidak boleh sama.',
            'nis_1.digits_between'   => 'NIS harus 10-12 digit angka.',
            'nis_2.digits_between'   => 'NIS harus 10-12 digit angka.',
        ]);

        $classId = $request->input('class_id');
        $nis1    = $request->input('nis_1');
        $nis2    = $request->input('nis_2');

        // Cek apakah kedua NIS ada di kelas yang dipilih
        $validCount = \App\Models\Student::where('class_id', $classId)
            ->whereIn('nis', [$nis1, $nis2])
            ->count();

        if ($validCount < 2) {
            return back()
                ->withInput()
                ->with('verification_error', 'Salah satu NIS tidak ditemukan di kelas ini.');
        }

        // Lanjut ke Step 6
        return redirect()->route('borrow.step6', [
            'grade'    => $request->input('grade'),
            'major_id' => $request->input('major_id'),
            'class_id' => $classId,
            'semester' => $request->input('semester'),
        ]);
    }
}
