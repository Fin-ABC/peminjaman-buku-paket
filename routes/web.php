<?php

use App\Http\Controllers\BorrowController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/student/menu', fn() => view('student.menu'))->name('student.menu');
// Route::get('/admin/login', fn() => view('admin'))->name('admin.login');

Route::prefix('borrow')->name('borrow.')->group(function () {
    Route::get('/step/1', [BorrowController::class, 'step1'])->name('step1');
    Route::get('/step/2', [BorrowController::class, 'step2'])->name('step2');
    Route::get('/step/3', [BorrowController::class, 'step3'])->name('step3');
    // Step 4-6 akan ditambah nanti
});
