<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\CustomAuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::match(['get', 'post'], 'login', [CustomAuthController::class, 'login'])->name('login')->middleware('guest');
Route::get('password/reset', [CustomAuthController::class, 'showLinkRequestForm'])->name('password.request');
