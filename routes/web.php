<?php

use App\Http\Controllers\OsobaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KunController;
use App\Http\Controllers\UdalostController;
use App\Http\Controllers\PrihlaskaController;
use App\Http\Controllers\Admin\UdalostController as AdminUdalostController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StartCislaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UdalostController::class, 'index']);
Route::view('/gdpr', 'gdpr')->name('gdpr');

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

    Route::get('/udalosti/{udalost}/prihlasit', [PrihlaskaController::class, 'create'])->name('prihlasky.create');
    Route::post('/udalosti/{udalost}/prihlasit', [PrihlaskaController::class, 'store'])
        ->middleware('throttle:prihlasky-store')
        ->name('prihlasky.store');

    Route::get('/prihlasky', [PrihlaskaController::class, 'index'])->name('prihlasky.index');
    Route::get('/prihlasky/{prihlaska}', [PrihlaskaController::class, 'show'])->name('prihlasky.show')->withTrashed();
    Route::get('/prihlasky/{prihlaska}/edit', [PrihlaskaController::class, 'edit'])->name('prihlasky.edit');
    Route::put('/prihlasky/{prihlaska}', [PrihlaskaController::class, 'update'])->name('prihlasky.update');
    Route::delete('/prihlasky/{prihlaska}', [PrihlaskaController::class, 'destroy'])->name('prihlasky.destroy');
    Route::get('/prihlasky/{prihlaska}/pdf', [PrihlaskaController::class, 'pdf'])->name('prihlasky.pdf')->withTrashed();

    Route::get('/ajax/osoba/{osoba}/polozky', [PrihlaskaController::class, 'ajaxOsobaPolozky']);
});

Route::prefix('/admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/udalosti', [AdminUdalostController::class, 'index'])->name('udalosti.index');
    Route::get('/udalosti/nova', [AdminUdalostController::class, 'create'])->name('udalosti.create');
    Route::post('/udalosti', [AdminUdalostController::class, 'store'])->name('udalosti.store');
    Route::get('/udalosti/{udalost}', [AdminUdalostController::class, 'show'])->name('udalosti.show');
    Route::get('/udalosti/{udalost}/edit', [AdminUdalostController::class, 'edit'])->name('udalosti.edit');
    Route::put('/udalosti/{udalost}', [AdminUdalostController::class, 'update'])->name('udalosti.update');
    Route::delete('/udalosti/{udalost}', [AdminUdalostController::class, 'destroy'])->name('udalosti.destroy');

    Route::post('/udalosti/{udalost}/moznosti', [AdminUdalostController::class, 'storeMoznost'])->name('udalosti.moznosti.store');
    Route::delete('/udalosti/{udalost}/moznosti/{moznost}', [AdminUdalostController::class, 'destroyMoznost'])->name('udalosti.moznosti.destroy');

    Route::post('/udalosti/{udalost}/ustajeni', [AdminUdalostController::class, 'storeUstajeni'])->name('udalosti.ustajeni.store');
    Route::delete('/udalosti/{udalost}/ustajeni/{ustajeni}', [AdminUdalostController::class, 'destroyUstajeni'])->name('udalosti.ustajeni.destroy');

    Route::get('/udalosti/{udalost}/prihlasky', [ReportController::class, 'prihlasky'])->name('reports.prihlasky');
    Route::put('/udalosti/{udalost}/prihlasky/{prihlaska}/start-cislo', [ReportController::class, 'updateStartCislo'])->name('reports.start-cislo.update');
    Route::put('/udalosti/{udalost}/prihlasky/start-cisla/normalizovat', [ReportController::class, 'normalizeStartCisla'])->name('reports.start-cisla.normalize');
    Route::get('/udalosti/{udalost}/prihlasky/smazane', [ReportController::class, 'smazane'])->name('reports.smazane');
    Route::get('/udalosti/{udalost}/startky', [ReportController::class, 'startky'])->name('reports.startky');
    Route::get('/udalosti/{udalost}/ubytovani', [ReportController::class, 'ubytovani'])->name('reports.ubytovani');

    Route::get('/udalosti/{udalost}/prihlasky/export/seznam', [ReportController::class, 'exportSeznam'])->name('reports.export.seznam');
    Route::get('/udalosti/{udalost}/prihlasky/export/discipliny', [ReportController::class, 'exportDiscipliny'])->name('reports.export.discipliny');
    Route::get('/udalosti/{udalost}/prihlasky/export/emaily', [ReportController::class, 'exportEmaily'])->name('reports.export.emaily');
    Route::get('/udalosti/{udalost}/prihlasky/export/kone', [ReportController::class, 'exportKone'])->name('reports.export.kone');
    Route::get('/udalosti/{udalost}/prihlasky/export/bulk-pdf', [ReportController::class, 'exportBulkPdf'])->name('reports.export.bulk-pdf');
    Route::get('/udalosti/{udalost}/startky/export', [ReportController::class, 'exportStartky'])->name('reports.export.startky');
    Route::get('/udalosti/{udalost}/startky/export/discipliny', [ReportController::class, 'exportDisciplinyPocty'])->name('reports.export.discipliny-pocty');
    Route::get('/udalosti/{udalost}/ubytovani/export', [ReportController::class, 'exportUstajeni'])->name('reports.export.ubytovani');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/gdpr-export', [AdminUserController::class, 'gdprExport'])->name('users.gdpr-export');
    Route::delete('/users/{user}/purge', [AdminUserController::class, 'purge'])->name('users.purge');

    Route::get('/start-cisla/{udalost}', [StartCislaController::class, 'show'])->name('start-cisla.show');
});

require __DIR__.'/auth.php';
