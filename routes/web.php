<?php

use App\Http\Controllers\OsobaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KunController;
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

    Route::get('/kone', [KunController::class, 'index'])->name('kone.index');
    Route::get('/kone/novy', [KunController::class, 'create'])->name('kone.create');
    Route::post('/kone', [KunController::class, 'store'])->name('kone.store');
    Route::get('/kone/{kun}/edit', [KunController::class, 'edit'])->name('kone.edit');
    Route::put('/kone/{kun}', [KunController::class, 'update'])->name('kone.update');
    Route::delete('/kone/{kun}', [KunController::class, 'destroy'])->name('kone.destroy');
});

require __DIR__.'/auth.php';
