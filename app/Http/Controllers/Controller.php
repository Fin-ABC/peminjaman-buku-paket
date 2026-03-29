<?php

namespace App\Http\Controllers;

use App\Imports\SubjectImport;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Facades\Excel;

abstract class Controller
{
    public function import(Request $request){
        $request->validate([
            'file' =>'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new SubjectImport, $request->file('file'));

        return back()->with('success', 'Import Berhasil');
    }
}
