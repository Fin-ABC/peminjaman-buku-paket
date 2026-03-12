<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/student/menu', fn() => view('welcome'))->name('student.menu');
// Route::get('/admin/login', fn() => view('admin'))->name('admin.login');

Route::get('/borrow/step/1', fn() => view('student.menu'))->name('borrow.step1');
Route::get('/return/step/1',  fn() => view('student.menu'))->name('return.step1');
