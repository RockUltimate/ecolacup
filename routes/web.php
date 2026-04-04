<?php

use App\Http\Controllers\OsobaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/ucet/edit', [ProfileController::class, 'edit'])->name('ucet.edit');
    Route::put('/ucet', [ProfileController::class, 'update'])->name('ucet.update');
    Route::delete('/ucet', [ProfileController::class, 'destroy'])->name('ucet.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/osoby', [OsobaController::class, 'index'])->name('osoby.index');
    Route::get('/osoby/nova', [OsobaController::class, 'create'])->name('osoby.create');
    Route::post('/osoby', [OsobaController::class, 'store'])->name('osoby.store');
    Route::get('/osoby/{osoba}/edit', [OsobaController::class, 'edit'])->name('osoby.edit');
    Route::put('/osoby/{osoba}', [OsobaController::class, 'update'])->name('osoby.update');
    Route::delete('/osoby/{osoba}', [OsobaController::class, 'destroy'])->name('osoby.destroy');
});

require __DIR__.'/auth.php';
