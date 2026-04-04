<?php

use App\Http\Controllers\OsobaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KunController;
use App\Http\Controllers\UdalostController;
use App\Http\Controllers\Admin\UdalostController as AdminUdalostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UdalostController::class, 'index']);

Route::get('/dashboard', function () {
    return redirect()->route('udalosti.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/udalosti', [UdalostController::class, 'index'])->name('udalosti.index');
Route::get('/udalosti/{udalost}', [UdalostController::class, 'show'])->name('udalosti.show');

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

Route::prefix('/admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/udalosti', [AdminUdalostController::class, 'index'])->name('udalosti.index');
    Route::get('/udalosti/nova', [AdminUdalostController::class, 'create'])->name('udalosti.create');
    Route::post('/udalosti', [AdminUdalostController::class, 'store'])->name('udalosti.store');
    Route::get('/udalosti/{udalost}/edit', [AdminUdalostController::class, 'edit'])->name('udalosti.edit');
    Route::put('/udalosti/{udalost}', [AdminUdalostController::class, 'update'])->name('udalosti.update');
    Route::delete('/udalosti/{udalost}', [AdminUdalostController::class, 'destroy'])->name('udalosti.destroy');

    Route::post('/udalosti/{udalost}/moznosti', [AdminUdalostController::class, 'storeMoznost'])->name('udalosti.moznosti.store');
    Route::delete('/udalosti/{udalost}/moznosti/{moznost}', [AdminUdalostController::class, 'destroyMoznost'])->name('udalosti.moznosti.destroy');

    Route::post('/udalosti/{udalost}/ustajeni', [AdminUdalostController::class, 'storeUstajeni'])->name('udalosti.ustajeni.store');
    Route::delete('/udalosti/{udalost}/ustajeni/{ustajeni}', [AdminUdalostController::class, 'destroyUstajeni'])->name('udalosti.ustajeni.destroy');
});

require __DIR__.'/auth.php';
