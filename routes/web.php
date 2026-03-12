<?php

use Illuminate\Support\Facades\Route;


Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/student/menu', fn() => view('welcome'))->name('student.menu');
// Route::get('/admin/login', fn() => view('admin'))->name('admin.login');

